<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LiveClassAccounts\StoreLiveClassAccountRequest as StoreRequest;
use App\Http\Requests\LiveClassAccounts\UpdateLiveClassAccountRequest as UpdateRequest;
use App\Services\LiveClassAccounts\LiveClassAccountService;
use App\Services\LiveClassIntegrations\LiveClassIntegrationService;
use Illuminate\Http\Request;

class LiveClassAccountController extends AdminBaseController
{

    public function __construct(
        private LiveClassAccountService $service,
        private LiveClassIntegrationService $liveClassIntegrationService
        ) {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'date_from', 'date_to']);
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
        // dd('h');
        return view('admin.live_class_accounts.index', [
            'page_title' => 'Live Class Integrations',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $integrations = $this->liveClassIntegrationService->getLiveClassIntegrationsOptions();
        return view('admin.live_class_accounts.create',[
            'integrations' => $integrations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return $this->successResponse('Item created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $liveClassAccount = $this->service->find($id);
        $integrations = $this->liveClassIntegrationService->getLiveClassIntegrationsOptions();

        return view('admin.live_class_accounts.edit', [
            'edit_data'     => $liveClassAccount,
            'integrations'  =>  $integrations
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
    }
    // bulk delete reviews
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
        if (!$result) {
            return $this->errorResponse('Failed to update sort order');
        }
        return $this->successResponse('Sort order updated successfully');
    }
    // Show sort view
    public function sortView()
    {
        $list_items = $this->service->getAll();
        
        return view('admin.live_class_accounts.sort', [
            'page_title' => 'Sort Live class accounts',
            'list_items' => $list_items,
        ]);
    }
    // clone item
    public function cloneItem($id)
    {
        $review = $this->service->find($id);

        $cloned = $this->service->clone($review);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action'  => 'modal', // or 'redirect'
            'url'     => route('admin.live_class_accounts.edit', $cloned->id),
        ]);
    }
}
