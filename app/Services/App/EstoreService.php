<?php

namespace App\Services\App;

use App\Services\Estore\EstoreCartService;
use App\Services\Estore\EstoreOrderItemService;
use App\Services\Estore\EstoreOrderService;
use App\Services\Estore\EstoreProductService;
use App\Services\Payments\RazorpayService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EstoreService extends AppBaseService
{
    public function __construct(
        protected EstoreOrderService $orderService,
        protected EstoreCartService $cartService,
        protected RazorpayService $razorpayService,
        protected EstoreOrderItemService $orderItemService,
        protected EstoreProductService $productService)
    {}


    public function createOrder(int $userId, array $data): array
    {
        return DB::transaction(function () use ($userId, $data) {
            
            $cartItems = $this->cartService->getCartItems($userId, true);
            
            if ($cartItems->isEmpty()) {
                return [
                    'status' => false,
                    'message' => 'Cart is empty',
                    'http_code' => Response::HTTP_OK
                ];
            }

            $totalAmount = 0;
            $discountAmount = 0;

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                
                if (!$product || $product->status != 1) {
                    return [
                        'status' => false,
                        'message' => "Product {$product->title} is not available",
                        'http_code' => Response::HTTP_OK
                    ];
                }

                if ($product->stock < $cartItem->quantity) {
                    return [
                        'status' => false,
                        'message' => "Insufficient stock for {$product->title}",
                        'http_code' => Response::HTTP_OK
                    ];
                }

                $itemTotal = $product->price * $cartItem->quantity;
                $totalAmount += $itemTotal;

                if ($product->mrp && $product->mrp > $product->price) {
                    $discountAmount += ($product->mrp - $product->price) * $cartItem->quantity;
                }
            }

            $payableAmount = $totalAmount;

            // Log::info('Payable amount: ' . $payableAmount);
            // Generate order number
            $orderNumber = $this->orderService->generateOrderNumber();
            // Order Data
            $orderData = [
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $data['payment_method'],
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'payable_amount' => $payableAmount,
                'notes' => json_encode($data['notes'] ?? null),
            ];

            if($data['payment_method'] == 'online') {

                // Create Razorpay order
                $razorpayReceipt = $this->orderService->generateReceipt();
                $razorpayOrder = $this->razorpayService->createOrder([
                    'amount' => $payableAmount,
                    'currency' => 'INR',
                    'receipt' => $razorpayReceipt,
                    'notes' => $data['notes']
                ]);
                if(!$razorpayOrder['status']) {
                    return [
                        'status' => false,
                        'message' => 'Failed to create order',
                        'http_code' => Response::HTTP_OK
                    ];
                }
                $orderData['payment_order_id'] = $razorpayOrder['order_id'];
            }

            // insert order
            $order = $this->orderService->store($orderData);

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                $this->orderItemService->store([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem->quantity,
                    'total_amount' => $product->price * $cartItem->quantity,
                ]);
            }

            if($data['payment_method'] == 'online') {
                return [
                    'status' => true,
                    'message' => 'Order created successfully',
                    'data' => [
                        'razorpay_order_id' => $razorpayOrder['order_id'],
                        'amount' => $razorpayOrder['amount'],
                        'currency' => 'INR',
                        'key_id' => config('services.razorpay.key_id'),
                        'notes' => $data['notes']
                    ],
                    'http_code' => Response::HTTP_OK
                ];
            }
            if($data['payment_method'] == 'cod') {
                // clear cart
                $this->cartService->clearCart($userId);
                // order items data
                $orderItems = $order->items->map(function($item) {
                    return [
                        'product_title' => $item->product->title,
                        'product_image' => $item->product->images_url[0] ?? null,
                        'quantity' => $item->quantity,
                        'total_amount' => $item->total_amount,
                    ];
                });
                return [
                    'status' => true,
                    'message' => 'Order created successfully',
                    'data' => [
                        'order_items' => $orderItems,
                        'amount_paid' => $order->payable_amount,
                    ],
                    'http_code' => Response::HTTP_OK
                ];
            }
        });
    }
    public function completeOrder(array $data): array
    {
        $result = $this->razorpayService->verifyPayment($data);

        if($result === false) {
            return [
                'status' => false,
                'message' => 'Payment verification failed',
                'http_code' => Response::HTTP_OK
            ];
        }

        $order = $this->orderService->getOrderByPaymentOrderId($data['razorpay_order_id']);

        if(!$order['status']) {
            return [
                'status' => false,
                'message' => $order['message'],
                'http_code' => Response::HTTP_OK
            ];
        }
        // update order
        $updateData = [
            'payment_status' => 'paid',
            'payment_id' => $data['razorpay_payment_id'],
        ];
        
        $order = $this->orderService->update($order['data']['id'], $updateData);
        $order->load('items.product');

        if(!$order) {
            return [
                'status' => false,
                'message' => 'Order update failed',
                'http_code' => Response::HTTP_OK
            ];
        }
        // update stock
        foreach($order->items as $item) {
            $this->productService->updateStock($item->product_id, $item->quantity);
        }

        // clear cart
        $this->cartService->clearCart($order->user_id);
        // order items data
        $orderItems = $order->items->map(function($item) {
            return [
                'product_title' => $item->product->title,
                'product_image' => $item->product->images_url[0] ?? null,
                'quantity' => $item->quantity,
                'total_amount' => $item->total_amount,
            ];
        });
        // Log::info('Order items: ' . json_encode($orderItems));
        return [
            'status' => true,
            'message' => 'Payment completed successfully',
            'data' => [
                'order_items' => $orderItems,
                'amount_paid' => $order->payable_amount,
            ],
            'http_code' => Response::HTTP_OK
        ];
    }
    // Get my orders
    public function getMyOrders(int $userId, int $perPage = 15)
    {
        return $this->orderService->getUserOrders($userId, $perPage);
    }
}
