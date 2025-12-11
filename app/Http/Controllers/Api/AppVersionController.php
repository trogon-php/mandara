<?php

namespace App\Http\Controllers\Api;

use App\Services\App\AppVersionService;

class AppVersionController extends BaseApiController
{
    public function __construct(
        protected AppVersionService $appVersionService
    ) {}

    /**
     * Return iOS & Android app versions
     * (no authentication required)
     */
    public function index()
    {
        $data = $this->appVersionService->getVersions();

        return $this->respondSuccess(
            $data['data'],
            $data['message']
        );
    }
}
