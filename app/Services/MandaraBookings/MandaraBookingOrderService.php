<?php

namespace App\Services\MandaraBookings;

use App\Models\MandaraBookingOrder;
use App\Services\Core\BaseService;
use Illuminate\Support\Str;

class MandaraBookingOrderService extends BaseService
{
    protected $modelClass = MandaraBookingOrder::class;
    protected string $receiptPrefix = 'mandara_booking_receipt_';


    public function getFilterConfig(): array
    {
        return [];
    }

    public function getSearchFieldsConfig(): array
    {
        return [];
    }

    public function getDefaultSearchFields(): array
    {
        return [];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
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

    public function generateReceipt(): string
    {
        return $this->receiptPrefix . substr(time(), -6) . strtoupper(Str::random(4));
    }
}
