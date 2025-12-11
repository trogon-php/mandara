<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Core\BaseService;
use App\Services\Users\UserMetaService;

class UserService extends BaseService
{
    protected string $modelClass = User::class;
    protected UserMetaService $userMetaService;

    public function __construct(UserMetaService $userMetaService)
    {
        parent::__construct();
        $this->userMetaService = $userMetaService;
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
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ]
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'description'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Find by phone and country code
     */
    public function findByPhone(string $phone, string $countryCode)
    {
        return $this->model->where('phone', $phone)->where('country_code', $countryCode)->first();
    }
    
    /**
     * Find by email
     */
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Store user with meta data - separates user table fields from meta fields
     */
    public function storeWithMeta(array $data): User
    {
        // Get enabled meta fields from config
        $enabledMetaFields = $this->userMetaService->getEnabledFields();
        
        // Define allowed user table fields
        $userTableFields = [
            'name', 'country_code', 'phone', 'email', 'role_id', 'status', 'profile_picture', 'password'
        ];
        
        // Separate user data (only user table fields)
        $userData = array_intersect_key($data, array_flip($userTableFields));
        
        // Get only enabled meta data (exclude from user table)
        $metaData = array_intersect_key($data, $enabledMetaFields);
        
        // Set default role_id for clients if not provided
        if (!isset($userData['role_id'])) {
            $userData['role_id'] = \App\Enums\Role::CLIENT->value;
        }
        
        // Set default status if not provided
        if (!isset($userData['status'])) {
            $userData['status'] = 'active';
        }
        // dd($userData);
        // Create user record (without meta data)
        $user = $this->model->create($userData);
        
        // Store meta data separately if any
        if (!empty($metaData)) {
            $this->userMetaService->storeUserMeta($user->id, $metaData, $user->id);
        }
        
        return $user;
    }
    /**
     * Update user with meta data - separates user table fields from meta fields
     */
    public function updateWithMeta(int $userId, array $data): ?User
    {
        // Get enabled meta fields from config
        $enabledMetaFields = $this->userMetaService->getEnabledFields();
        
        // Define allowed user table fields
        $userTableFields = [
            'name', 'country_code', 'phone', 'email', 'role_id', 'status', 'profile_picture', 'password'
        ];
        
        // Separate user data (only user table fields)
        $userData = array_intersect_key($data, array_flip($userTableFields));
        
        // Get only enabled meta data (exclude from user table)
        $metaData = array_intersect_key($data, $enabledMetaFields);
        // dd($userData, $metaData);
        // Find the existing user
        $user = $this->model->find($userId);
        if (!$user) {
            return null;
        }
        // dd($userData, $metaData);
        // Update user record (without meta data)
        $user->update($userData);
        
        // Store/update meta data separately if any
        if (!empty($metaData)) {
            $this->userMetaService->storeUserMeta($user->id, $metaData, $user->id);
        }
        // dd($user->fresh());
        return $user->fresh();
    }
    /**
     * Get active users for selection
     */
    public function getActiveUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('status', 'active')->get();
    }
}
