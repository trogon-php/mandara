<?php

namespace App\Http\Controllers\Api;

use App\Services\Packages\PackageService;
use Illuminate\Http\Request;

class PackageController extends BaseApiController
{
    public function __construct(protected PackageService $packageService) {}

    /**
     * Get all active packages
     */
    public function index(Request $request)
    {
        $params = $request->only(['search', 'has_offer', 'min_price', 'max_price', 'duration_days', 'sort_field', 'sort_direction']);
        
        if (!empty($params)) {
            $data = $this->packageService->getPackagesWithFilters($params);
        } else {
            $data = $this->packageService->getActivePackagesForApi();
        }

        return $this->respondSuccess(
            $data, 
            'Packages fetched successfully'
        );
    }

    /**
     * Get package details
     */
    public function show($id)
    {
        $data = $this->packageService->getPackageDetailsForApi($id);

        if (empty($data)) {
            return $this->respondError('Package not found', 404);
        }

        return $this->respondSuccess(
            $data, 
            'Package details fetched successfully'
        );
    }

    /**
     * Get packages by course ID
     */
    public function getByCourseId($courseId)
    {
        $data = $this->packageService->getPackagesByCourseId($courseId);

        return $this->respondSuccess(
            $data, 
            'Packages fetched successfully'
        );
    }

    /**
     * Get packages by course unit ID
     */
    public function getByUnitId($unitId, Request $request)
    {
        $includeParent = $request->boolean('include_parent', false);
        
        $data = $this->packageService->getPackagesByUnitId($unitId, $includeParent);

        return $this->respondSuccess(
            $data, 
            'Packages fetched successfully'
        );
    }

    /**
     * Get packages by course material ID
     */
    public function getByMaterialId($materialId, Request $request)
    {
        $includeParent = $request->boolean('include_parent', false);
        
        $data = $this->packageService->getPackagesByMaterialId($materialId, $includeParent);

        return $this->respondSuccess(
            $data, 
            'Packages fetched successfully'
        );
    }

    /**
     * Get packages by category ID
     */
    public function getByCategoryId($categoryId)
    {
        $data = $this->packageService->getPackagesByCategoryId($categoryId);

        return $this->respondSuccess(
            $data, 
            'Packages fetched successfully'
        );
    }

    /**
     * Get packages by multiple criteria
     */
    public function getByCriteria(Request $request)
    {
        $criteria = $request->only(['course_ids', 'unit_ids', 'material_ids', 'category_ids']);
        
        // Convert comma-separated strings to arrays if needed
        foreach ($criteria as $key => $value) {
            if (is_string($value)) {
                $criteria[$key] = array_map('intval', explode(',', $value));
            }
        }

        $data = $this->packageService->getPackagesByCriteria($criteria);

        return $this->respondSuccess(
            $data, 
            'Packages fetched successfully'
        );
    }

    /**
     * Get packages with advanced filters
     */
    public function getWithFilters(Request $request)
    {
        $params = $request->only([
            'search', 
            'has_offer', 
            'min_price', 
            'max_price', 
            'duration_days', 
            'sort_field', 
            'sort_direction'
        ]);

        $data = $this->packageService->getPackagesWithFilters($params);

        return $this->respondSuccess(
            $data, 
            'Packages fetched successfully'
        );
    }
}
