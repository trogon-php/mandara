<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DietPlans\AppDietPlanResource;
use App\Http\Resources\DietPlans\AppDietPlansListResource;
use App\Services\DietPlans\DietPlanService;
use Illuminate\Http\Request;

class DietPlanApiController extends BaseApiController
{
    public function __construct(private DietPlanService $service) {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $month = $request->get('month');

        $dietPlans = $this->service->getActiveDietPlansPaginated($month, $perPage);

        $dietPlans = AppDietPlansListResource::collection($dietPlans);
        return $this->respondPaginated($dietPlans, 'Diet plans fetched successfully');
    }
    public function show(Request $request, $id)
    {
        $dietPlan = $this->service->find($id);
        if (!$dietPlan) {
            return $this->respondError('Diet plan not found', 404);
        }
        $dietPlan = AppDietPlanResource::make($dietPlan);
        return $this->respondSuccess($dietPlan, 'Diet plan fetched successfully');
    }
}
