<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MealPackage\AppMealPackageResource;
use App\Services\MealPackages\MealPackageService;
use App\Services\Users\UserMetaService;
use Illuminate\Http\Request;

class MealPackageApiController extends BaseApiController
{
    public function __construct(
        private MealPackageService $service,
        private UserMetaService $userMetaService
        ) {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $mealPackages = $this->service->getAll();

        $selectedMealPackageId = $this->getAuthUser()->id 
            ? $this->userMetaService->getUserMetaValue($this->getAuthUser()->id, 'meal_package_id')
            : null;
        // attach in request
        $request->merge(['selected_meal_package_id' => $selectedMealPackageId]);
        $mealPackages = AppMealPackageResource::collection($mealPackages);

        return $this->respondSuccess($mealPackages, 'Meal packages fetched successfully');
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'meal_package_id' => 'required|exists:meal_packages,id',
        ]);
        $userId = $this->getAuthUser()->id;

        $success = $this->userMetaService->updateUserMetaValue(
            $userId,
            'meal_package_id',
            $request->meal_package_id,
            $userId
        );

        if (!$success) {
            return $this->respondError('Failed to store or update meal package', 500);
        }

        return $this->respondSuccess(null, 'Meal package stored or updated successfully');
    }
}
