<?php

namespace App\Services\Food;

use App\Models\FoodOrder;
use App\Services\Core\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FoodOrderService extends BaseService
{
    protected string $modelClass = FoodOrder::class;
    protected string $orderNumberPrefix = 'FOOD-';
    
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
            'order_date' => [
                'type' => 'date',
                'label' => 'Order Date',
                'col' => 3,
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'order_number' => 'Order Number',
            'payment_order_id' => 'Payment Order ID',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['order_number', 'payment_order_id'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    public function getUserOrders(int $userId, int $perPage = 15)
    {
        return $this->model->where('user_id', $userId)
            ->with('items.item.category')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getOrderDetails(int $userId, int $orderId): array
    {
        $order = $this->model->where('user_id', $userId)
            ->with('items.item.category')
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

        if (!$order) {
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

    public function generateOrderNumber(): string
    {
        return DB::transaction(function () {
            $lastOrder = $this->model
                ->whereNotNull('order_number')
                ->where('order_number', 'like', $this->orderNumberPrefix . '%')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $nextNumber = 1;

            if ($lastOrder && $lastOrder->order_number) {
                $lastNumber = str_replace($this->orderNumberPrefix, '', $lastOrder->order_number);
                $nextNumber = (int) $lastNumber + 1;
            }

            return $this->orderNumberPrefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}