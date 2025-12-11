<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\ReelCategories\ReelCategoryService;
use App\Http\Requests\ReelCategories\StoreReelCategoryRequest as StoreRequest;
use App\Http\Requests\ReelCategories\UpdateReelCategoryRequest as UpdateRequest;

class ReelCategoryController extends AdminBaseController
{
    protected ReelCategoryService $service;

    public function __construct(ReelCategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        if($filters){
            $list_items = $this->service->getFilteredData($params);
        }else{
            $list_items = $this->service->getAll();
        }

        return view('admin.reel_categories.index', [
            'page_title' => 'Reel Categories',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        return view('admin.reel_categories.create');
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Reel category added successfully');
    }

    public function edit($id)
    {
        $edit_data = $this->service->find($id);

        return view('admin.reel_categories.edit', [
            'edit_data' => $edit_data,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Reel category updated successfully');
    }

    public function show($id)
    {
        return view('admin.reel_categories.show', [
            'item' => $this->service->find($id),
        ]);
    }

    public function sortView(Request $request)
    {
        return view('admin.reel_categories.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        return $this->successResponse('Sort order updated successfully');
    }

    public function destroy($id)
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

    public function cloneItem($id)
    {
        $item = $this->service->find($id);

        $cloned = $this->service->clone($item);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal', // or 'redirect'
            'url' => route('admin.reel_categories.edit', $cloned->id),
        ]);
    }
}
