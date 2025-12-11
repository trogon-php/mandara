<?php

namespace App\Services\App;

use App\Services\App\AppBaseService;

class AppVersionService extends AppBaseService
{
    protected string $cachePrefix = 'app_versions';
    protected int $defaultTtl = 60;

    /**
     * Get app versions (iOS & Android) from config
     */
    public function getVersions(): array
    {
        return $this->remember("versions", function () {
            return $this->payload([
                'ios' => config('app_versions.ios'),
                'android' => config('app_versions.android'),
            ], 'App versions fetched successfully');
        });
    }
}
