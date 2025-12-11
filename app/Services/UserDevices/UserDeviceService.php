<?php

namespace App\Services\UserDevices;

use App\Models\UserDevice;
use App\Services\Core\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserDeviceService extends BaseService
{
    protected string $modelClass = UserDevice::class;

    public function __construct()
    {
        parent::__construct();
    }

    public function getFilterConfig(): array
    {
        return [
            'platform' => [
                'type' => 'exact',
                'label' => 'Platform',
                'col' => 3,
                'options' => [
                    'android' => 'Android',
                    'ios' => 'iOS',
                    'web' => 'Web',
                ],
            ],
            'is_active' => [
                'type' => 'exact',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'device_id' => 'Device ID',
            'device_name' => 'Device Name',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['device_id', 'device_name'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }
    /**
     * Register or update device token
     */
    public function registerDevice(int $userId, array $data): ?Model
    {
        return UserDevice::updateOrCreate(
            [
                'user_id' => $userId,
                'device_id' => $data['device_id'],
            ],
            [
                'fcm_token' => $data['fcm_token'],
                'platform' => $data['platform'] ?? 'android',
                'device_name' => $data['device_name'] ?? null,
                'app_version' => $data['app_version'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Get active devices for a user
     */
    public function getActiveDevicesForUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get active devices for multiple users
     */
    public function getActiveDevicesForUsers(array $userIds): Collection
    {
        return $this->model->whereIn('user_id', $userIds)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get all devices for a user
     */
    public function getDevicesForUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * Remove device token
     */
    public function removeDevice(int $userId, string $deviceId): bool
    {
        return $this->model->where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->delete();
    }

    /**
     * Mark device as inactive
     */
    public function markDeviceInactive(?int $userId, ?string $deviceId, ?string $fcmToken): bool
    {
        if (empty(array_filter([$userId, $deviceId, $fcmToken]))) {
            return false;
        }
        
        return $this->model
            ->when($userId, fn($query) => $query->where('user_id', $userId))
            ->when($deviceId, fn($query) => $query->where('device_id', $deviceId))
            ->when($fcmToken, fn($query) => $query->where('fcm_token', $fcmToken))
            ->update(['is_active' => false]);
    }

    /**
     * Mark device as active
     */
    public function markDeviceActive(?int $userId, ?string $deviceId, ?string $fcmToken): bool
    {
        if (empty(array_filter([$userId, $deviceId, $fcmToken]))) {
            return false;
        }
        
        return $this->model
            ->when($userId, fn($query) => $query->where('user_id', $userId))
            ->when($deviceId, fn($query) => $query->where('device_id', $deviceId))
            ->when($fcmToken, fn($query) => $query->where('fcm_token', $fcmToken))
            ->update([
                'is_active' => true,
                'last_used_at' => now(),
            ]);
    }

    /**
     * Remove invalid token
     */
    public function removeInvalidToken(string $fcmToken): bool
    {
        return $this->model->where('fcm_token', $fcmToken)
            ->update(['is_active' => false]);
    }

    /**
     * Get FCM tokens for user
     */
    public function getFcmTokensForUser(int $userId): array
    {
        return $this->getActiveDevicesForUser($userId)
            ->pluck('fcm_token')
            ->toArray();
    }

    /**
     * Get FCM tokens for multiple users
     */
    public function getFcmTokensForUsers(array $userIds): array
    {
        return $this->getActiveDevicesForUsers($userIds)
            ->pluck('fcm_token')
            ->toArray();
    }
}
