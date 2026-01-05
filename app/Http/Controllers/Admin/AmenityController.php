<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Amenity\StoreAmenityRequest as StoreRequest;
use App\Http\Requests\Amenity\UpdateAmenityRequest as UpdateRequest;
use App\Http\Requests\Amenity\StoreAmenityItemRequest as StoreItemRequest;
use App\Http\Requests\Amenity\UpdateAmenityItemRequest as UpdateItemRequest;
use App\Services\Amenities\AmenityService;
use App\Services\Amenities\AmenityItemService;
use Illuminate\Http\Request;

class AmenityController extends AdminBaseController
{
    public function __construct(private AmenityService $service,
    private AmenityItemService $itemService)
    {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['status']));
       
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData(['search' => $searchParams['search'], 'filters' => $filters]);

        return view('admin.amenities.index', [
            'page_title' => 'Amenity List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create() 
    { 
        return view('admin.amenities.create'); 
    }

    public function store(StoreRequest $request)
    {
       
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }

    public function edit(string $id)
    {
        $edit_data = $this->service->findForEdit($id);
        return view('admin.amenities.edit', compact('edit_data'));
    }

    public function update(UpdateRequest $request, string $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Item updated successfully');
    }

    public function destroy(string $id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete items');
        }
        return $this->successResponse('Selected items deleted successfully');
    }

    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        if (!$result) return $this->errorResponse('Failed to update sort order');
        return $this->successResponse('Sort order updated successfully');
    }

    public function sortView()
    {
        $list_items = $this->service->getAll();
        return view('admin.amenities.sort', ['list_items'=>$list_items]);
    }
}