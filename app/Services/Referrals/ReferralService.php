<?php

namespace App\Services\Referrals;

use App\Models\Referral;
use App\Models\User;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\DB;

class ReferralService extends BaseService
{
    protected string $modelClass = Referral::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration
     */
    public function getFilterConfig(): array
    {
        return [
            'referrer_id' => [
                'type' => 'exact',  
                'label' => 'Referrer',
                'col' => 4,
                'options' => $this->getReferrerOptions(),
            ],
            'date_from' => [
                'type' => 'date_range',
                'label' => 'Date Range',
                'col' => 6,
                'field' => 'created_at',
            ],
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'referrer.name' => 'Referrer Name',
            'referrer.email' => 'Referrer Email',
            'referrer.phone' => 'Referrer Phone',
            'referred.name' => 'Referred User Name',
            'referred.email' => 'Referred User Email',
            'referral_code' => 'Referral Code',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['referrer.name', 'referred.name', 'referral_code'];
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
        $query = $this->model->with(['referrer', 'referred']);

        // Apply search
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function($q) use ($search) {
                $q->where('referral_code', 'like', "%{$search}%")
                  ->orWhereHas('referrer', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('referred', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if (!empty($params['filters'])) {
            foreach ($params['filters'] as $field => $value) {
                if (!empty($value)) {
                    // Handle date range filters
                    if ($field === 'date_from') {
                        $query->whereDate('created_at', '>=', $value);
                    } elseif ($field === 'date_to') {
                        $query->whereDate('created_at', '<=', $value);
                    } else {
                        $query->where($field, $value);
                    }
                }
            }
        }

        // Apply sorting
        $sorting = $params['sorting'] ?? $this->getDefaultSorting();
        $query->orderBy($sorting['field'], $sorting['direction']);

        return $query->paginate($params['per_page'] ?? 15);
    }

    /**
     * Get referrer options for dropdown
     */
    public function getReferrerOptions(): array
    {
        return User::select('id', 'name', 'email')
            ->whereHas('referrals')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($user) {
                $label = $user->name . ' (' . $user->email . ')';
                return [$user->id => $label];
            })
            ->toArray();
    }

    /**
     * Get top referrers with statistics
     */
    public function getTopReferrers(array $params = [])
    {
        $query = User::select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                DB::raw('COUNT(referrals.id) as total_referrals'),
                DB::raw('SUM(referrals.reward_coins) as total_reward_points')
            )
            ->join('referrals', 'users.id', '=', 'referrals.referrer_id')
            ->whereNull('referrals.deleted_at')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone');

        // Apply search
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.phone', 'like', "%{$search}%");
            });
        }

        // Apply date filters
        if (!empty($params['filters'])) {
            if (!empty($params['filters']['date_from'])) {
                $query->whereDate('referrals.created_at', '>=', $params['filters']['date_from']);
            }
            if (!empty($params['filters']['date_to'])) {
                $query->whereDate('referrals.created_at', '<=', $params['filters']['date_to']);
            }
        }

        // Order by total referrals by default
        $sortBy = $params['sort_by'] ?? 'total_referrals';
        $sortDir = $params['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($params['per_page'] ?? 15);
    }

    /**
     * Get top referrers filter configuration
     */
    public function getTopReferrersFilterConfig(): array
    {
        return [
            'date_from' => [
                'type' => 'date_range',
                'label' => 'Date Range',
                'col' => 12,
            ],
        ];
    }

    /**
     * Get referral statistics
     */
    public function getReferralStats(): array
    {
        return [
            'total_referrals' => $this->model->count(),
            'pending_referrals' => $this->model->where('status', 'pending')->count(),
            'completed_referrals' => $this->model->where('status', 'completed')->count(),
            'rewarded_referrals' => $this->model->where('status', 'rewarded')->count(),
            'total_reward_coins' => $this->model->sum('reward_coins'),
        ];
    }
}



