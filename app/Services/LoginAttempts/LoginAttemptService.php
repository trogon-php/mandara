<?php

namespace App\Services\LoginAttempts;

use App\Models\LoginAttempt;
use App\Services\Core\BaseService;

class LoginAttemptService extends BaseService
{
    protected string $modelClass = LoginAttempt::class;

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
            'status' => [
                'type' => 'select',  
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    'pending' => 'Pending',
                    'verified' => 'Verified',
                    'failed' => 'Failed',
                    'expired' => 'Expired',
                ],
            ],
            'channel' => [
                'type' => 'select',  
                'label' => 'Channel',
                'col' => 3,
                'options' => [
                    'phone' => 'Phone',
                    'email' => 'Email',
                ],
            ],
            'created_at' => [
                'type' => 'date-range',
                'label' => 'Date Range',
                'col' => 4,
                'fromField' => 'date_from',
                'toField' => 'date_to',
            ],
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'email' => 'Email',
            'phone' => 'Phone',
            'ip_address' => 'IP Address',
            'otp_code' => 'OTP Code',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['email', 'phone', 'ip_address'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
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
     * Get filtered data with pagination
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->with(['user']);

        // Apply search
        if (!empty($params['search'])) {
            $searchFields = $params['search_fields'] ?? $this->getSearchConfig()['default_search_fields'];
            $query->where(function ($q) use ($params, $searchFields) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'like', '%' . $params['search'] . '%');
                }
            });
        }

        // Apply filters
        if (!empty($params['filters'])) {
            foreach ($params['filters'] as $field => $value) {
                if ($value !== null && $value !== '') {
                    switch ($field) {
                        case 'status':
                        case 'channel':
                            $query->where($field, $value);
                            break;
                        case 'date_from':
                            $query->whereDate('created_at', '>=', $value);
                            break;
                        case 'date_to':
                            $query->whereDate('created_at', '<=', $value);
                            break;
                    }
                }
            }
        }

        // Default sorting by created_at desc
        $query->orderBy('created_at', 'desc');

        return $query->paginate(15);
    }
}
