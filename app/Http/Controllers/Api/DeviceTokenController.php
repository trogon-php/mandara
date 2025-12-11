<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserDevices\AppUserDeviceResource;
use App\Services\UserDevices\UserDeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends BaseApiController
{
    public function __construct(protected UserDeviceService $userDeviceService)
    {}

    /**
     * Register or update device token
     * POST /api/v1/devices/register
     */
    public function register(Request $request)
    {
        $user = $this->getAuthUser();

        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255',
            'fcm_token' => 'required|string',
            'platform' => 'required|in:android,ios,web',
            'device_name' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->respondValidationError('Validation failed', $validator->errors()->toArray());
        }
        // dd($validator->validated());
        try {
            $device = $this->userDeviceService->registerDevice($user->id, $validator->validated());
            
            $device = new AppUserDeviceResource($device);

            return $this->respondSuccess($device, 'Device registered successfully');

        } catch (\Exception $e) {
            return $this->respondError('Failed to register device' .$e->getMessage(), 500);
        }
    }

    /**
     * Get user's devices
     * GET /api/v1/devices
     */
    public function index(Request $request)
    {
        $user = $this->getAuthUser();
        $devices = $this->userDeviceService->getDevicesForUser($user->id);

        return $this->respondSuccess([
            'devices' => $devices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'platform' => $device->platform,
                    'device_name' => $device->device_name,
                    'app_version' => $device->app_version,
                    'is_active' => $device->is_active,
                    'last_used_at' => $device->last_used_at?->toISOString(),
                    'created_at' => $device->created_at->toISOString(),
                ];
            }),
        ], 'Devices fetched successfully');
    }

    /**
     * Remove device token
     * DELETE /api/v1/devices/{deviceId}
     */
    public function destroy(Request $request, string $deviceId)
    {
        $user = $this->getAuthUser();

        try {
            $result = $this->userDeviceService->removeDevice($user->id, $deviceId);

            if ($result) {
                return $this->respondSuccess(null, 'Device removed successfully');
            }

            return $this->respondError('Device not found', 404);
        } catch (\Exception $e) {
            return $this->respondError('Failed to remove device', $e->getMessage(), 500);
        }
    }
}
