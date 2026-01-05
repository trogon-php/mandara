<?php

namespace App\Services\App;

use App\Services\Food\FoodCartService;
use App\Services\Food\FoodOrderItemService;
use App\Services\Food\FoodOrderService;
use App\Services\Food\FoodItemService;
use App\Services\Payments\RazorpayService;
use App\Services\Users\UserService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FoodService extends AppBaseService
{
    public function __construct(
        protected FoodOrderService $orderService,
        protected FoodCartService $cartService,
        protected RazorpayService $razorpayService,
        protected FoodOrderItemService $orderItemService,
        protected FoodItemService $itemService,
        protected UserService $userService)
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
                $item = $cartItem->item;
                
                if (!$item || $item->status != 1) {
                    return [
                        'status' => false,
                        'message' => "Item {$item->title} is not available",
                        'http_code' => Response::HTTP_OK
                    ];
                }

                if ($item->stock < $cartItem->quantity) {
                    return [
                        'status' => false,
                        'message' => "Insufficient stock for {$item->title}",
                        'http_code' => Response::HTTP_OK
                    ];
                }

                $itemTotal = $item->price * $cartItem->quantity;
                $totalAmount += $itemTotal;
            }

            $payableAmount = $totalAmount;

            // Generate order number
            $orderNumber = $this->orderService->generateOrderNumber();
            
            // Order Data
            $orderData = [
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'order_date' => now()->format('Y-m-d'),
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $data['payment_method'],
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'payable_amount' => $payableAmount,
                'delivery_room' => $data['notes']['delivery_room'] ?? null,
                'notes' => json_encode($data['notes'] ?? null),
            ];

            if($data['payment_method'] == 'online') {
                // Create Razorpay order
                $razorpayReceipt = $this->orderService->generateOrderNumber();
                $razorpayOrder = $this->razorpayService->createOrder([
                    'amount' => $payableAmount,
                    'currency' => 'INR',
                    'receipt' => $razorpayReceipt,
                    'notes' => $data['notes']
                ]);
                $orderData['payment_order_id'] = $razorpayOrder['order_id'];
            }

            // insert order
            $order = $this->orderService->store($orderData);

            foreach ($cartItems as $cartItem) {
                $item = $cartItem->item;
                $this->orderItemService->store([
                    'order_id' => $order->id,
                    'food_item_id' => $item->id,
                    'item_title' => $item->title,
                    'item_description' => $item->short_description,
                    'food_type' => $item->is_veg ? 'veg' : 'non_veg',
                    'unit_price' => $item->price,
                    'quantity' => $cartItem->quantity,
                    'total_amount' => $item->price * $cartItem->quantity,
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
                // Update stock
                foreach($cartItems as $cartItem) {
                    $this->itemService->updateStock($cartItem->item->id, $cartItem->quantity);
                }
                
                // clear cart
                $this->cartService->clearCart($userId);
                
                // order items data
                $orderItems = $order->items->map(function($item) {
                    return [
                        'item_title' => $item->item_title,
                        'item_description' => $item->item_description,
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
        
        $order = $this->orderService->update($order['data']->id, $updateData);
        $order->load('items.item');

        if(!$order) {
            return [
                'status' => false,
                'message' => 'Order update failed',
                'http_code' => Response::HTTP_OK
            ];
        }
        
        // update stock
        foreach($order->items as $item) {
            $this->itemService->updateStock($item->food_item_id, $item->quantity);
        }

        // clear cart
        $this->cartService->clearCart($order->user_id);
        
        // order items data
        $orderItems = $order->items->map(function($item) {
            return [
                'item_title' => $item->item_title,
                'item_description' => $item->item_description,
                'quantity' => $item->quantity,
                'total_amount' => $item->total_amount,
            ];
        });
        
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
    
    public function getMyOrders(int $userId, int $perPage = 15)
    {
        return $this->orderService->getUserOrders($userId, $perPage);
    }

    public function updateMealConfiguration(int $userId, array $data): array
    {
        $result = $this->userService->updateWithMeta($userId, $data);

        if($result) {
            return [
                'status' => true,
                'message' => 'Meal configuration updated successfully',
                'http_code' => Response::HTTP_OK
            ];
        }
        return [
            'status' => false,
            'message' => 'Meal configuration update failed',
            'http_code' => Response::HTTP_OK
        ];
    }
}