<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EstoreProducts\StoreEstoreProductRequest as StoreRequest;
use App\Http\Requests\EstoreProducts\UpdateEstoreProductRequest as UpdateRequest;
use App\Services\Estore\EstoreProductService;
use Illuminate\Http\Request;
use App\Models\EstoreCategory;

class EstoreProductController extends AdminBaseController
{
    public function __construct(private EstoreProductService $service) {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['status','date_from','date_to']));
       
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData(['search' => $searchParams['search'], 'filters' => $filters]);

        return view('admin.estore_products.index', [
            'page_title' => 'Estore Product List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create() 
    { 
        $estoreCategories = $this->service->getCategoryOptions();
        // Remove the 'All Categories' option for form use
        unset($estoreCategories['']);
        return view('admin.estore_products.create', compact('estoreCategories')); 
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }

    public function edit(string $id)
    {
        $estoreCategories = $this->service->getCategoryOptions();
        $edit_data = $this->service->find($id);
        return view('admin.estore_products.edit', compact('edit_data', 'estoreCategories'));
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
        return view('admin.estore_products.sort', ['list_items'=>$list_items]);
    }

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);
        if (!$cloned) return $this->errorResponse('Failed to clone item.');
        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.estore_products.edit', $cloned->id)
        ]);
    }
}