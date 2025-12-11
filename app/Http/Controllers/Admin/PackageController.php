<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Packages\PackageService;
use App\Http\Requests\Packages\StorePackageRequest as StoreRequest;
use App\Http\Requests\Packages\UpdatePackageRequest as UpdateRequest;
use Illuminate\Support\Facades\Log;

class PackageController extends AdminBaseController
{
    protected PackageService $service;

    public function __construct(PackageService $service)
    {
        $this->service = $service;
    }

    // List all packages
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'has_offer','system_generated']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Remove null or empty string filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);

        // Load items relationship for each package
        $list_items->getCollection()->load('items');

        return view('admin.packages.index', [
            'page_title' => 'Packages',
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
        return view('admin.packages.create', [
            'page_title' => 'Add Package',
        ]);
    }

    // Handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Package added successfully');
    }

    // Show edit form (AJAX modal)
    public function edit($id)
    {
        $package = $this->service->find($id);

        return view('admin.packages.edit', [
            'page_title' => 'Edit Package',
            'edit_data' => $package,
        ]);
    }

    // Handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Package updated successfully');
    }

    // Show single package
    public function show($id)
    {
        return view('admin.packages.show', [
            'page_title' => 'Package Details',
            'package' => $this->service->find($id),
        ]);
    }

    // Get package details for AJAX
    public function getDetails($package)
    {
        Log::info('Package details requested for ID: ' . $package);
        
        $package = $this->service->find($package);
        
        if (!$package) {
            Log::warning('Package not found for ID: ' . $package);
            return response()->json(['success' => false, 'message' => 'Package not found'], 404);
        }

        Log::info('Package found:', [
            'id' => $package->id,
            'title' => $package->title,
            'price' => $package->price,
            'offer_price' => $package->offer_price,
        ]);

        return response()->json([
            'success' => true,
            'package' => [
                'id' => $package->id,
                'title' => $package->title,
                'price' => $package->price,
                'offer_price' => $package->offer_price,
            ]
        ]);
    }

    // Show sort view
    public function sortView(Request $request)
    {
        return view('admin.packages.sort', [
            'page_title' => 'Sort Packages',
            'list_items' => $this->service->getAll(),
        ]);
    }

    // Handle sort update
    public function sortUpdate(Request $request)
    {
        $this->service->sortUpdate($request->order);
        return $this->successResponse('Package order updated successfully');
    }

    // Handle bulk delete
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $deleted = $this->service->bulkDelete($ids);
        
        return $this->successResponse("{$deleted} package(s) deleted successfully");
    }

    // Handle clone
    public function cloneItem($id)
    {
        $package = $this->service->find($id);
        if (!$package) {
            return $this->errorResponse('Package not found');
        }

        $data = $package->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);
        $data['title'] = $package->title . ' (Copy)';

        $this->service->store($data);
        return $this->successResponse('Package cloned successfully');
    }
}
