<?php

namespace App\Services\Payments;

use App\Models\CottagePackage;
use App\Models\UserPayment;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Core\BaseService;

class PaymentService extends BaseService
{
    protected $modelClass = Payment::class;

    /**
     * Get students options for dropdown
     */
    public function getStudentsOptions(): array
    {
        return User::select('id', 'name', 'phone', 'country_code')
            ->where('role_id', 2) // Assuming role_id 2 is for students
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($user) {
                $phone = $user->phone ? " [+{$user->country_code} {$user->phone}]" : '';
                $label = $user->name . $phone;
                return [$user->id => $label];
            })
            ->toArray();
    }

    /**
     * Get packages options for dropdown
     */
    public function getPackagesOptions(): array
    {
        return CottagePackage::select('id', 'title', 'price', 'discount_amount')
            ->where('status', 'active')
            ->orderBy('title')
            ->get()
            ->mapWithKeys(function ($package) {
                $discount = $package->discount_amount ?? 0;
                $payable = max(0, (float) $package->price - (float) $discount);

                $label = $package->title . " (₹{$payable})";
                return [$package->id => $label];
            })
            ->toArray();
    }

    /**
     * Get orders options for dropdown
     */
    public function getOrdersOptions(): array
    {
        return Order::select('id', 'order_number', 'amount_final', 'status')
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->mapWithKeys(function ($order) {
                $label = $order->order_number . " - " . ($order->user->name ?? 'N/A') . " (₹{$order->amount_final})";
                return [$order->id => $label];
            })
            ->toArray();
    }

    /**
     * Get filter configuration
     */
    public function getFilterConfig(): array
    {
        return [
            'payment_status' => [
                'type' => 'exact',
                'label' => 'Payment Status',
                'col' => 4,
                'options' => [
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ],
            ],
            'package_id' => [
                'type' => 'exact',
                'label' => 'Package',
                'col' => 4,
                'options' => $this->getPackagesOptions(),
            ],
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'transaction_id' => 'Transaction ID',
            'remarks' => 'Remarks',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['transaction_id', 'remarks'];
    }

    /**
     * Get search configuration
     */
    public function getSearchConfig(): array
    {
        return [
            'search_fields' => $this->getSearchFieldsConfig(),
            'default_search_fields' => $this->getDefaultSearchFields(),
        ];
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats(): array
    {
        $totalPayments = $this->model::count();
        $pendingPayments = $this->model::where('payment_status', 'pending')->count();
        $paidPayments = $this->model::where('payment_status', 'paid')->count();
        $failedPayments = $this->model::where('payment_status', 'failed')->count();
        $refundedPayments = $this->model::where('payment_status', 'refunded')->count();

        $totalAmount = $this->model::where('payment_status', 'paid')->sum('amount_paid');
        $pendingAmount = $this->model::where('payment_status', 'pending')->sum('amount_total');

        return [
            'total_payments' => $totalPayments,
            'pending_payments' => $pendingPayments,
            'paid_payments' => $paidPayments,
            'failed_payments' => $failedPayments,
            'refunded_payments' => $refundedPayments,
            'total_amount' => $totalAmount,
            'pending_amount' => $pendingAmount,
        ];
    }

    /**
     * Get payments by status
     */
    public function getPaymentsByStatus($status)
    {
        return $this->model::with(['user', 'package', 'order'])
            ->where('payment_status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update payment status
     */
    public function updateStatus($id, $status)
    {
        $payment = $this->find($id);
        
        if (!$payment) {
            return false;
        }

        $payment->update([
            'payment_status' => $status,
            'updated_by' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Get filtered data with search and filters
     */
    public function getFilteredData($params = [])
    {
        $query = $this->model::with(['user', 'package', 'order']);

        // Apply search
        if (!empty($params['search'])) {
            $searchFields = $params['search_fields'] ?? $this->getDefaultSearchFields();
            $query->where(function ($q) use ($params, $searchFields) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'like', '%' . $params['search'] . '%');
                }
            });
        }

        // Apply filters
        if (!empty($params['filters'])) {
            foreach ($params['filters'] as $field => $value) {
                if (!empty($value)) {
                    $query->where($field, $value);
                }
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Get default sorting configuration
     */
    public function getDefaultSorting(): array
    {
        return [
            'field' => 'created_at',
            'direction' => 'desc'
        ];
    }
}
