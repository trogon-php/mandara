<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CottagePackages\StoreCottagePackageRequest as StoreRequest;
use App\Http\Requests\CottagePackages\UpdateCottagePackageRequest as UpdateRequest;
use App\Services\CottageCategories\CottageCategoryService;
use App\Services\CottagePackages\CottagePackageService;
use App\Services\Cottages\CottageService;
use Illuminate\Http\Request;

class CottagePackageController extends AdminBaseController
{
    public function __construct(
        private CottagePackageService $service,
        private CottageService $cottageService,
        private CottageCategoryService $cottageCategoryService
    ) {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['status','date_from','date_to']));
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData(['search' => $searchParams['search'], 'filters' => $filters]);

        return view('admin.cottage_packages.index', [
            'page_title' => 'Cottage Packages',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    { 
        // $cottages = $this->cottageService->getOptions();
        $cottageCategories = $this->cottageCategoryService->getOptions();

        return view('admin.cottage_packages.create', compact('cottageCategories')); 
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }

    public function edit(string $id)
    {
        $edit_data = $this->service->find($id);
        $cottageCategories = $this->cottageCategoryService->getOptions();
        return view('admin.cottage_packages.edit', compact('edit_data', 'cottageCategories'));
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
        return view('admin.cottage_packages.sort', ['list_items'=>$list_items]);
    }

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);
        if (!$cloned) return $this->errorResponse('Failed to clone item.');
        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.cottage_packages.edit', $cloned->id)
        ]);
    }
}