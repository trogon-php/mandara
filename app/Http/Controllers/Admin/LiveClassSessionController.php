<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LiveClassSessions\StoreLiveClassSessionRequest as StoreRequest;
use App\Http\Requests\LiveClassSessions\UpdateLiveClassSessionRequest as UpdateRequest;
use App\Services\LiveClasses\LiveClassService;
use App\Services\LiveClassSessions\LiveClassSessionService;
use Illuminate\Http\Request;

class LiveClassSessionController extends AdminBaseController
{
    public function __construct(
        private LiveClassSessionService $service,
        private LiveClassService $liveClassService
        ) {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['live_class_id']));
        
        // Handle date range filter
        if ($request->has('date_from') || $request->has('date_to')) {
            $dateRange = [];
            if ($request->has('date_from')) {
                $dateRange['from'] = $request->get('date_from');
            }
            if ($request->has('date_to')) {
                $dateRange['to'] = $request->get('date_to');
            }
            if (!empty($dateRange)) {
                $filters['date_range'] = $dateRange;
            }
        }
        
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData([
            'search' => $searchParams['search'],
            'filters' => $filters,
            'sort_by' => 'id',
            'sort_dir' => 'desc',
        ]);

        return view('admin.live_class_sessions.index', [
            'page_title' => 'LiveClassSession List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create() { 
        $liveClasses = $this->liveClassService->getClassesOptions();
        return view('admin.live_class_sessions.create',[
            'liveClasses' => $liveClasses,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }

    public function edit(string $id)
    {
        $edit_data = $this->service->find($id);
        $liveClasses = $this->liveClassService->getClassesOptions();
        
        return view('admin.live_class_sessions.edit', compact('edit_data','liveClasses'));
    }

    public function editModal(string $id)
    {
        $edit_data = $this->service->find($id);
        $liveClasses = $this->liveClassService->getClassesOptions();
        
        return view('admin.live_class_sessions.edit-modal', compact('edit_data','liveClasses'));
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
        return view('admin.live_class_sessions.sort', ['list_items'=>$list_items]);
    }

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);
        if (!$cloned) return $this->errorResponse('Failed to clone item.');
        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.live_class_sessions.edit', $cloned->id)
        ]);
    }
}