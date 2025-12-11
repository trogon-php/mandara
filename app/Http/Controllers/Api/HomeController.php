<?php

namespace App\Http\Controllers\Api;

use App\Services\App\HomeService;

class HomeController extends BaseApiController
{
    public function __construct(protected HomeService $homeService) {}

    public function index()
    {
        $data = $this->homeService->getHomeData();

        return $this->respondSuccess(
            $data['data'], 
            $data['message']
        );
    }
}
