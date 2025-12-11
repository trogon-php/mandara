<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Packages\PackageFeatureService;
use App\Http\Requests\PackageFeatures\StorePackageFeatureRequest as StoreRequest;
use App\Http\Requests\PackageFeatures\UpdatePackageFeatureRequest as UpdateRequest;

class PackageFeatureController extends AdminBaseController
{
    protected PackageFeatureService $service;

    public function __construct(PackageFeatureService $service)
    {
        $this->service = $service;
    }

    // List all package features
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'package_id']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);

        return view('admin.package-features.index', [
            'page_title' => 'Package Features',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    // Show add form (AJAX modal)
    public function create()
    {
        $packages = $this->service->getPackageOptions();
        
        return view('admin.package-features.create', [
            'page_title' => 'Add Package Feature',
            'packages' => $packages,
        ]);
    }

    // Handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Package feature added successfully');
    }

    // Show edit form (AJAX modal)
    public function edit($id)
    {
        $packageFeature = $this->service->find($id);
        $packages = $this->service->getPackageOptions();

        return view('admin.package-features.edit', [
            'page_title' => 'Edit Package Feature',
            'edit_data' => $packageFeature,
            'packages' => $packages,
        ]);
    }

    // Handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Package feature updated successfully');
    }

    // Show single package feature
    public function show($id)
    {
        return view('admin.package-features.show', [
            'page_title' => 'Package Feature Details',
            'packageFeature' => $this->service->find($id),
        ]);
    }

    // Show sort view
    public function sortView(Request $request)
    {
        return view('admin.package-features.sort', [
            'page_title' => 'Sort Package Features',
            'list_items' => $this->service->getAll(),
        ]);
    }

    // Handle sort update
    public function sortUpdate(Request $request)
    {
        $this->service->sortUpdate($request->order);
        return $this->successResponse('Package feature order updated successfully');
    }

    // Handle bulk delete
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $deleted = $this->service->bulkDelete($ids);
        
        return $this->successResponse("{$deleted} package feature(s) deleted successfully");
    }

    // Handle clone
    public function cloneItem($id)
    {
        $packageFeature = $this->service->find($id);
        if (!$packageFeature) {
            return $this->errorResponse('Package feature not found');
        }

        $data = $packageFeature->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);
        $data['title'] = $packageFeature->title . ' (Copy)';

        $this->service->store($data);
        return $this->successResponse('Package feature cloned successfully');
    }
}

