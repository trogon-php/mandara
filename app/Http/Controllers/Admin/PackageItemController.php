<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Packages\PackageItemService;
use App\Services\Packages\PackageService;
use App\Services\Courses\CourseService;
use App\Http\Requests\PackageItems\StorePackageItemRequest as StoreRequest;
use App\Http\Requests\PackageItems\UpdatePackageItemRequest as UpdateRequest;

class PackageItemController extends AdminBaseController
{
    protected PackageItemService $service;
    protected PackageService $packageService;
    protected CourseService $courseService;

    public function __construct(
        PackageItemService $service,
        PackageService $packageService,
        CourseService $courseService
    ) {
        $this->service = $service;
        $this->packageService = $packageService;
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of package items
     */
    public function index(Request $request)
    {
        $filters = $request->only(['item_type', 'status', 'package_id']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
            'paginate' => true,
            'per_page' => 15
        ];

        $list_items = $this->service->getFilteredData($params);

        return view('admin.package-items.index', [
            'page_title' => 'Package Items',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    /**
     * Show the form for creating a new package item
     */
    public function create(Request $request)
    {
        $packageId = $request->get('package_id') ?? null;
        $packagesOptions = $this->packageService->getPackageOptions();
        $coursesOptions = $this->courseService->getCoursesOptions();
        
        return view('admin.package-items.create', [
            'packagesOptions' => $packagesOptions,
            'coursesOptions' => $coursesOptions,
            'packageId' => $packageId,
        ]);
    }

    /**
     * Store a newly created package item
     */
    public function store(StoreRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return $this->successResponse('Package item created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified package item
     */
    public function show($id)
    {
        $packageItem = $this->service->find((int) $id, ['package']);
        
        if (!$packageItem) {
            return $this->errorResponse('Package item not found');
        }

        return view('admin.package-items.show', [
            'item' => $packageItem,
        ]);
    }

    /**
     * Show the form for editing the specified package item
     */
    public function edit($id)
    {
        $packageItem = $this->service->find((int) $id);
        $packages = $this->packageService->getAll();
        $courses = $this->courseService->getAll();

        if (!$packageItem) {
            return $this->errorResponse('Package item not found');
        }

        return view('admin.package-items.edit', [
            'edit_data' => $packageItem,
            'packages' => $packages,
            'courses' => $courses,
        ]);
    }

    /**
     * Update the specified package item
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            $this->service->update((int) $id, $request->validated());
            return $this->successResponse('Package item updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified package item
     */
    public function destroy($id)
    {
        if (!$this->service->delete((int) $id)) {
            return $this->errorResponse('Failed to delete package item');
        }
        return $this->successResponse('Package item deleted successfully');
    }

    /**
     * Bulk delete package items
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return $this->errorResponse('No items selected for deletion');
        }

        $deletedCount = $this->service->bulkDelete($ids);
        
        if ($deletedCount > 0) {
            return $this->successResponse("Successfully deleted {$deletedCount} package item(s)");
        }
        
        return $this->errorResponse('No package items were deleted');
    }

    /**
     * Show sort view for package items
     */
    public function sortView(Request $request)
    {
        $packageId = $request->get('package_id');
        
        if ($packageId) {
            $list_items = $this->service->getItemsForPackage($packageId);
        } else {
            $list_items = $this->service->getAll();
        }

        return view('admin.package-items.sort', [
            'list_items' => $list_items,
            'package_id' => $packageId,
        ]);
    }

    /**
     * Handle sort update for package items
     */
    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        if (!$result) {
            return $this->errorResponse('Failed to update sort order');
        }
        return $this->successResponse('Sort order updated successfully');
    }

    /**
     * Get available items for a specific type (AJAX)
     */
    public function getAvailableItems(Request $request)
    {
        $itemType = $request->get('item_type');
        
        if (!$itemType) {
            return response()->json(['items' => []]);
        }

        $items = $this->service->getAvailableItems($itemType);
        
        return response()->json(['items' => $items]);
    }

    /**
     * Get items for a specific package (AJAX)
     */
    public function getItemsForPackage(Request $request)
    {
        $packageId = $request->get('package_id');
        
        if (!$packageId) {
            return response()->json(['items' => []]);
        }

        $items = $this->service->getItemsForPackage($packageId);
        
        return response()->json(['items' => $items]);
    }

}