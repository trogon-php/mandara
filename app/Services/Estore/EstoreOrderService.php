<?php

namespace App\Services\Estore;

use App\Models\EstoreOrder;
use App\Services\Core\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstoreOrderService extends BaseService
{
    protected string $modelClass = EstoreOrder::class;
    protected string $orderNumberPrefix = 'ESTOR-';
    protected string $receiptPrefix = 'estore_order_receipt_';
    
    public function getFilterConfig(): array
    {
        return [
            'order_status' => [
                'type' => 'select',
                'label' => 'Order Status',
                'col' => 3,
                'options' => [
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'processing' => 'Processing',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ],
            ],
            'payment_status' => [
                'type' => 'select',
                'label' => 'Payment Status',
                'col' => 3,
                'options' => [
                    'unpaid' => 'Unpaid',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ],
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'payment_order_id' => 'Order ID',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['payment_order_id'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    // public function createOrder(int $userId, array $data): array
    // {
    //     $cartItems = EstoreCart::where('user_id', $userId)->with('product')->get();
        
    //     if ($cartItems->isEmpty()) {
    //         return [
    //             'status' => false,
    //             'message' => 'Cart is empty',
    //             'http_code' => Response::HTTP_OK
    //         ];
    //     }

    //     $totalAmount = 0;
    //     $discountAmount = 0;

    //     foreach ($cartItems as $cartItem) {
    //         $product = $cartItem->product;
            
    //         if (!$product || $product->status != 1) {
    //             return [
    //                 'status' => false,
    //                 'message' => "Product {$product->title} is not available",
    //                 'http_code' => Response::HTTP_OK
    //             ];
    //         }

    //         if ($product->stock < $cartItem->quantity) {
    //             return [
    //                 'status' => false,
    //                 'message' => "Insufficient stock for {$product->title}",
    //                 'http_code' => Response::HTTP_OK
    //             ];
    //         }

    //         $itemTotal = $product->price * $cartItem->quantity;
    //         $totalAmount += $itemTotal;

    //         if ($product->mrp && $product->mrp > $product->price) {
    //             $discountAmount += ($product->mrp - $product->price) * $cartItem->quantity;
    //         }
    //     }

    //     $payableAmount = $totalAmount;

    //     $orderId = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(8));

    //     $order = $this->model->create([
    //         'user_id' => $userId,
    //         'order_status' => 'pending',
    //         'payment_status' => 'unpaid',
    //         'payment_method' => $data['payment_method'] ?? 'cod',
    //         'payment_order_id' => $orderId,
    //         'total_amount' => $totalAmount,
    //         'discount_amount' => $discountAmount,
    //         'payable_amount' => $payableAmount,
    //         'notes' => $data['notes'] ?? null,
    //     ]);

    //     foreach ($cartItems as $cartItem) {
    //         $product = $cartItem->product;
    //         EstoreOrderItem::create([
    //             'order_id' => $orderId,
    //             'product_id' => $product->id,
    //             'unit_price' => $product->price,
    //             'quantity' => $cartItem->quantity,
    //             'total_amount' => $product->price * $cartItem->quantity,
    //         ]);

    //         // Update stock
    //         $product->stock -= $cartItem->quantity;
    //         $product->save();
    //     }

    //     // Clear cart
    //     EstoreCart::where('user_id', $userId)->delete();

    //     return [
    //         'status' => true,
    //         'message' => 'Order created successfully',
    //         'data' => $order->load('items.product'),
    //         'http_code' => Response::HTTP_OK
    //     ];
    // }

    public function getUserOrders(int $userId, int $perPage = 15)
    {
        return $this->model->where('user_id', $userId)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getOrderDetails(int $userId, int $orderId): array
    {
        $order = $this->model->where('user_id', $userId)
            ->with('items.product.category')
            ->find($orderId);

        if (!$order) {
            return [
                'status' => false,
                'message' => 'Order not found',
                'http_code' => Response::HTTP_NOT_FOUND
            ];
        }

        return [
            'status' => true,
            'data' => $order,
            'http_code' => Response::HTTP_OK
        ];
    }

    public function getOrderByPaymentOrderId(string $paymentOrderId): array
    {
        $order = $this->model->where('payment_order_id', $paymentOrderId)->first();

        if(!$order) {
            return [
                'status' => false,
                'message' => 'Order not found',
            ];
        }

        return [
            'status' => true,
            'data' => $order,
        ];
    }
    /**
     * Generate unique order number
     */
    public function generateOrderNumber(): string
    {
        // Use database lock to prevent race conditions
        return DB::transaction(function () {
            // Get the last order number
            $lastOrder = $this->model
                ->whereNotNull('order_number')
                ->where('order_number', 'like', $this->orderNumberPrefix . '%')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $nextNumber = 1;

            if ($lastOrder && $lastOrder->order_number) {
                // Extract numeric part from last order number (e.g., "ESTOR-00001" -> 1)
                $lastNumber = str_replace($this->orderNumberPrefix, '', $lastOrder->order_number);
                
                // Convert to integer and increment
                $nextNumber = (int) $lastNumber + 1;
            }

            // Generate new order number with zero padding
            return $this->orderNumberPrefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function generateReceipt(): string
    {
        return $this->receiptPrefix . time() . '-' . strtoupper(Str::random(8));
    }

}