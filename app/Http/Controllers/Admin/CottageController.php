<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Cottages\StoreCottageRequest as StoreRequest;
use App\Http\Requests\Cottages\UpdateCottageRequest as UpdateRequest;
use App\Services\Cottages\CottageService;
use Illuminate\Http\Request;

class CottageController extends AdminBaseController
{
    public function __construct(private CottageService $service) {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['status','date_from','date_to']));
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData(['search' => $searchParams['search'], 'filters' => $filters]);

        return view('admin.cottages.index', [
            'page_title' => 'Cottage List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create() 
    { 
        $cottageCategories = $this->service->getCottageCategoryOptions();
        return view('admin.cottages.create', compact('cottageCategories')); 
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }

    public function edit(string $id)
    {
        $cottageCategories = $this->service->getCottageCategoryOptions();
        $edit_data = $this->service->find($id);
        return view('admin.cottages.edit', compact('edit_data', 'cottageCategories'));
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
        return view('admin.cottages.sort', ['list_items'=>$list_items]);
    }

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);
        if (!$cloned) return $this->errorResponse('Failed to clone item.');
        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.cottages.edit', $cloned->id)
        ]);
    }
}