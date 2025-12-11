<?php

namespace App\Services\Orders;

use App\Models\CottagePackage;
use App\Models\Order;
use App\Models\User;
use App\Models\Package;
use App\Models\Coupon;
use App\Services\Core\BaseService;
use App\Services\Users\StudentService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    protected string $modelClass = Order::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration
     */
    public function getFilterConfig(): array
    {
        if(request('user_id')) {
            $user = app(StudentService::class)->find(request('user_id'));
            $default = [
                'key' => $user->id,
                'label' => $user->name . ' [+' . $user->country_code . ' ' . $user->phone . ']'
            ];
        }
        return [
            'user_id' => [
                'type' => 'select2-ajax',  
                'id' => 'filter_user_id',
                'label' => 'Student',
                'col' => 3,
                'ajax_url' => route('admin.students.select2-ajax-options'),
                'default' => $default ?? null,
            ],
            'package_id' => [
                'type' => 'exact',  
                'label' => 'Package',
                'id' => 'filter_package_id',
                'col' => 3,
                'options' => $this->getPackagesOptions(),
            ],
            'status' => [
                'type' => 'exact',  
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    'pending' => 'Pending',
                    'partially_paid' => 'Partially Paid',
                    'paid' => 'Paid',
                    'cancelled' => 'Cancelled',
                    'refunded' => 'Refunded',
                ],
            ],
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'order_number' => 'Order Number',
            'user.name' => 'User Name',
            'user.email' => 'User Email',
            'user.phone' => 'User Phone',
            'package.title' => 'Package Title',
            'coupon_code' => 'Coupon Code',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['order_number', 'user.name', 'user.email', 'package.title'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    /**
     * Get filtered data with relationships
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->with(['user', 'package', 'coupon']);

        // Apply search
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('coupon_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('package', function($packageQuery) use ($search) {
                      $packageQuery->where('title', 'like', "%{$search}%");
                  });
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

        // Apply sorting
        $sorting = $params['sorting'] ?? $this->getDefaultSorting();
        $query->orderBy($sorting['field'], $sorting['direction']);

        return $query->paginate(15);
    }

    /**
     * Store order with validation
     */
    public function store(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Generate order number if not provided
            if (empty($data['order_number'])) {
                $data['order_number'] = $this->generateOrderNumber();
            }

            // Calculate amounts if not provided
            if (empty($data['amount_final'])) {
                $data['amount_final'] = $this->calculateFinalAmount($data);
            }

            return parent::store($data);
        });
    }

    /**
     * Update order with validation
     */
    public function update(int $id, array $data): ?Order
    {
        return DB::transaction(function () use ($id, $data) {
            // Recalculate amounts if package or coupon changed
            if (isset($data['package_id']) || isset($data['coupon_id'])) {
                $data['amount_final'] = $this->calculateFinalAmount($data);
            }

            return parent::update($id, $data);
        });
    }

    /**
     * Generate unique order number
     */
    public function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        } while ($this->model->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Calculate final amount based on package and coupon
     */
    public function calculateFinalAmount(array $data): float
    {
        $package = Package::find($data['package_id']);
        if (!$package) {
            throw new \Exception('Package not found');
        }

        $totalAmount = $package->offer_price ?? $package->price;
        $finalAmount = $totalAmount;

        // Apply coupon discount if provided
        if (!empty($data['coupon_id'])) {
            $coupon = Coupon::find($data['coupon_id']);
            if ($coupon && $coupon->status === 'active') {
                if ($coupon->discount_type === 'percentage') {
                    $discount = ($totalAmount * $coupon->discount_value) / 100;
                } else {
                    $discount = $coupon->discount_value;
                }
                $finalAmount = max(0, $totalAmount - $discount);
            }
        }

        return round($finalAmount, 2);
    }

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
        return CottagePackage::select('id', 'title')
            ->where('status', 'active')
            ->orderBy('title')
            ->get()
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * Get coupons options for dropdown
     */
    public function getCouponsOptions(): array
    {
        return Coupon::select('id', 'code', 'title')
            ->where('status', 'active')
            ->orderBy('code')
            ->get()
            ->mapWithKeys(function ($coupon) {
                $label = $coupon->code;
                if ($coupon->title) {
                    $label .= ' - ' . $coupon->title;
                }
                return [$coupon->id => $label];
            })
            ->toArray();
    }

    /**
     * Get order statistics
     */
    public function getOrderStats(): array
    {
        return [
            'total_orders' => $this->model->count(),
            'pending_orders' => $this->model->where('status', 'pending')->count(),
            'paid_orders' => $this->model->where('status', 'paid')->count(),
            'cancelled_orders' => $this->model->where('status', 'cancelled')->count(),
            'total_revenue' => $this->model->where('status', 'paid')->sum('amount_final'),
        ];
    }

    /**
     * Update order status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $order = $this->model->find($id);
        if (!$order) {
            return false;
        }

        $order->update(['status' => $status]);
        return true;
    }

    /**
     * Get orders by status
     */
    public function getOrdersByStatus(string $status)
    {
        return $this->model->with(['user', 'package', 'coupon'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
