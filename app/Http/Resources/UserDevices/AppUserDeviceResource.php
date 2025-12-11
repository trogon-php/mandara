<?php

namespace App\Http\Resources\UserDevices;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppUserDeviceResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = false;
        $this->includeAudit = false;
    }

    public function resourceFields(Request $request): array
    {
        return [
            'device_id' => $this->device_id,
            'fcm_token' => $this->fcm_token,
            'platform' => $this->platform,
            'device_name' => $this->device_name,
            'app_version' => $this->app_version,
            'is_active' => $this->is_active,
        ];
    }
}
