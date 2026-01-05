<?php

namespace App\Http\Controllers\Api;

use App\Services\App\MandaraDashboardService;

class MandaraDashboardController extends BaseApiController
{
    public function __construct(protected MandaraDashboardService $mandaraDashboardService) {}


    public function index()
    {
        $user = $this->getAuthUser();
        $dashboard = $this->mandaraDashboardService->getDashboard($user->id);
        return $this->respondSuccess($dashboard, 'Dashboard fetched successfully');
    }

    public function getBabyDashboard()
    {
        $user = $this->getAuthUser();
        $dashboard = $this->mandaraDashboardService->getBabyDashboard($user->id);
        return $this->respondSuccess($dashboard, 'Baby dashboard fetched successfully');
    }

    public function getMotherDashboard()
    {
        $user = $this->getAuthUser();
        $dashboard = $this->mandaraDashboardService->getMotherDashboard($user->id);
        return $this->respondSuccess($dashboard, 'Mother dashboard fetched successfully');
    }
}
