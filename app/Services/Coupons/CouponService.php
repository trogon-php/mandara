<?php

namespace App\Services\Coupons;

use App\Models\Coupon;
use App\Services\Core\BaseService;
use App\Services\Traits\CacheableService;

class CouponService extends BaseService
{
    use CacheableService;

    protected $modelClass = Coupon::class;

    /**
     * Get filter configuration for admin panel
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'expired' => 'Expired',
                ],
                'multiple' => true,
            ],
            'date_range' => [
                'type' => 'date_range',
                'label' => 'Date Range',
                'fields' => ['start_date', 'end_date'],
            ],
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'code' => [
                'type' => 'text',
                'label' => 'Coupon Code',
                'placeholder' => 'Enter coupon code',
            ],
            'title' => [
                'type' => 'text',
                'label' => 'Title',
                'placeholder' => 'Enter title',
            ],
            'description' => [
                'type' => 'text',
                'label' => 'Description',
                'placeholder' => 'Enter description',
            ],
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['code', 'title', 'description'];
    }

    /**
     * Get default sorting configuration
     */
    public function getDefaultSorting(): array
    {
        return [
            'field' => 'created_at',
            'direction' => 'desc',
        ];
    }

    /**
     * Apply custom filters
     */
    protected function applyFilters($query, array $filters)
    {
        // Status filter
        if (isset($filters['status']) && !empty($filters['status'])) {
            if (in_array('expired', $filters['status'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('end_date', '<', now())
                      ->orWhereIn('status', array_diff($filters['status'], ['expired']));
                });
            } else {
                $query->whereIn('status', $filters['status']);
            }
        }

        // Discount type filter
        if (isset($filters['discount_type']) && !empty($filters['discount_type'])) {
            $query->whereIn('discount_type', $filters['discount_type']);
        }

        // Date range filter
        if (isset($filters['start_date']) && !empty($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date']) && !empty($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        // Usage limit filter
        if (isset($filters['usage_limit']) && !empty($filters['usage_limit'])) {
            if ($filters['usage_limit'] === 'unlimited') {
                $query->whereNull('usage_limit');
            } elseif ($filters['usage_limit'] === 'limited') {
                $query->whereNotNull('usage_limit');
            }
        }
    }

    /**
     * Get active coupons
     */
    public function getActiveCoupons()
    {
        return $this->model->active()->get();
    }

    /**
     * Get expired coupons
     */
    public function getExpiredCoupons()
    {
        return $this->model->expired()->get();
    }

    /**
     * Validate coupon code
     */
    public function validateCoupon(string $code, int $userId, int $packageId): array
    {
        $coupon = $this->model->where('code', $code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Invalid coupon code.',
            ];
        }

        if (!$coupon->isActive()) {
            return [
                'valid' => false,
                'message' => 'Coupon is not active or has expired.',
            ];
        }

        if ($coupon->hasReachedUsageLimit()) {
            return [
                'valid' => false,
                'message' => 'Coupon usage limit has been reached.',
            ];
        }

        if ($coupon->hasUserReachedLimit($userId)) {
            return [
                'valid' => false,
                'message' => 'You have reached the maximum usage limit for this coupon.',
            ];
        }

        if (!$coupon->isValidForPackage($packageId)) {
            return [
                'valid' => false,
                'message' => 'This coupon is not valid for the selected package.',
            ];
        }

        if (!$coupon->isValidForUser($userId)) {
            return [
                'valid' => false,
                'message' => 'This coupon is not available for your account.',
            ];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'message' => 'Coupon is valid.',
        ];
    }

    /**
     * Apply coupon to order
     */
    public function applyCoupon(int $couponId, int $userId, int $orderId, float $orderAmount): array
    {
        $coupon = $this->model->find($couponId);
        
        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Coupon not found.',
            ];
        }

        $discountAmount = $coupon->calculateDiscount($orderAmount);
        $finalAmount = $orderAmount - $discountAmount;

        // Record the usage
        \App\Models\CouponUsage::create([
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'order_id' => $orderId,
            'used_at' => now(),
        ]);

        return [
            'success' => true,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'coupon' => $coupon,
        ];
    }

    /**
     * Get coupon usage statistics
     */
    public function getUsageStats(int $couponId): array
    {
        $coupon = $this->model->find($couponId);
        
        if (!$coupon) {
            return [];
        }

        return [
            'total_usage' => $coupon->usages()->count(),
            'unique_users' => $coupon->usages()->distinct('user_id')->count(),
            'recent_usage' => $coupon->usages()->where('used_at', '>=', now()->subDays(30))->count(),
            'usage_by_month' => $coupon->usages()
                ->selectRaw('DATE_FORMAT(used_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('count', 'month')
                ->toArray(),
        ];
    }

    /**
     * Get coupons expiring soon
     */
    public function getExpiringSoon(int $days = 7)
    {
        return $this->model->where('end_date', '<=', now()->addDays($days))
            ->where('end_date', '>=', now())
            ->where('status', 'active')
            ->get();
    }

    /**
     * Get popular coupons (most used)
     */
    public function getPopularCoupons(int $limit = 10)
    {
        return $this->model->withCount('usages')
            ->orderBy('usages_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Find coupon by code
     */
    public function findByCode(string $code): ?Coupon
    {
        return $this->model->where('code', $code)->first();
    }

    /**
     * Get search configuration for UI
     */
    public function getSearchConfig(): array
    {
        return [
            'search_fields' => [
                'code' => 'Coupon Code',
                'title' => 'Title', 
                'description' => 'Description'
            ],
            'default_search_fields' => $this->getDefaultSearchFields(),
        ];
    }

    /**
     * Attach packages to a coupon
     */
    public function attachPackages(int $couponId, array $packageIds): void
    {
        $coupon = $this->find($couponId);
        if ($coupon) {
            $coupon->packages()->attach($packageIds);
        }
    }

    /**
     * Detach packages from a coupon
     */
    public function detachPackages(int $couponId, array $packageIds): void
    {
        $coupon = $this->find($couponId);
        if ($coupon) {
            $coupon->packages()->detach($packageIds);
        }
    }

    /**
     * Sync packages for a coupon (replace all existing)
     */
    public function syncPackages(int $couponId, array $packageIds): void
    {
        $coupon = $this->find($couponId);
        if ($coupon) {
            $coupon->packages()->sync($packageIds);
        }
    }

    /**
     * Attach users to a coupon
     */
    public function attachUsers(int $couponId, array $userIds): void
    {
        $coupon = $this->find($couponId);
        if ($coupon) {
            $coupon->users()->attach($userIds);
        }
    }

    /**
     * Detach users from a coupon
     */
    public function detachUsers(int $couponId, array $userIds): void
    {
        $coupon = $this->find($couponId);
        if ($coupon) {
            $coupon->users()->detach($userIds);
        }
    }

    /**
     * Sync users for a coupon (replace all existing)
     */
    public function syncUsers(int $couponId, array $userIds): void
    {
        $coupon = $this->find($couponId);
        if ($coupon) {
            $coupon->users()->sync($userIds);
        }
    }

    /**
     * Get all coupons with relationships for listing
     */
    public function getAllWithRelations(array $relations = []): \Illuminate\Database\Eloquent\Collection
    {
        $defaultRelations = ['usages', 'packages', 'users'];
        $relations = array_merge($defaultRelations, $relations);
        
        return $this->model->with($relations)->sorted()->get();
    }

    /**
     * Check if coupon code exists
     */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $query = $this->model->where('code', $code);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Get coupons that have been used (for bulk operations)
     */
    public function getUsedCoupons(array $ids): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->whereIn('id', $ids)
            ->whereHas('usages')
            ->get();
    }

    /**
     * Get coupon with specific relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->model->with($relations)->find($id);
    }
}
