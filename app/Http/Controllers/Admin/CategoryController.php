<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Categories\CategoryService;
use App\Http\Requests\Categories\StoreCategoryRequest as StoreRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest as UpdateRequest;
use App\Models\Category;

class CategoryController extends AdminBaseController
{
    protected CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'parent_id']);
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
            $list_items = $this->service->getFlatList();
        }

        return view('admin.categories.index', [
            'page_title' => 'Categories',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        $parentCategories = $this->service->getFlatList()->pluck('indented_title', 'id');
        
        return view('admin.categories.create', [
            'parentCategories' => $parentCategories,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Category added successfully');
    }

    public function edit($id)
    {
        $edit_data = $this->service->find($id);
        $parentCategories = $this->service->getFlatList()->pluck('indented_title', 'id');

        return view('admin.categories.edit', [
            'edit_data' => $edit_data,
            'parentCategories' => $parentCategories,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Category updated successfully');
    }

    public function show($id)
    {
        return view('admin.categories.show', [
            'item' => $this->service->find($id),
        ]);
    }

    public function sortView(Request $request)
    {
        return view('admin.categories.sort', [
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
            'url' => route('admin.categories.edit', $cloned->id),
        ]);
    }
}
