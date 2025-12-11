<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LiveClasses\StoreLiveClassRequest as StoreRequest;
use App\Http\Requests\LiveClasses\UpdateLiveClassRecurrenceRequest;
use App\Http\Requests\LiveClasses\UpdateLiveClassRequest as UpdateRequest;
use App\Services\LiveClassAccounts\LiveClassAccountService;
use App\Services\LiveClasses\LiveClassService;
use App\Services\Courses\CourseService;
use Illuminate\Http\Request;

class LiveClassController extends AdminBaseController
{
    public function __construct(
        private LiveClassService $service,
        private LiveClassAccountService $liveClassAccountService,
        private CourseService $courseService
        ) {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['date_from','date_to','account_id','course_id']));
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData([
            'search' => $searchParams['search'], 
            'filters' => $filters,
            'sort_by' => 'id',
            'sort_dir' => 'desc',
        ]);

        return view('admin.live_classes.index', [
            'page_title' => 'LiveClass List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create() {
        
        $accounts = $this->liveClassAccountService->getAccountsOptions();
        $courses = $this->courseService->getCoursesOptions();

        return view('admin.live_classes.create',[
            'accounts' => $accounts,
            'courses' => $courses
        ]); 
    }

    public function store(StoreRequest $request)
    {
        // dd($request->validated());
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }

    public function edit(string $id)
    {
        $edit_data = $this->service->find($id);
        $edit_data->load('sessions'); // Load sessions relationship
        $accounts = $this->liveClassAccountService->getAccountsOptions();
        $courses = $this->courseService->getCoursesOptions();
        $edit_data->recurrence_rule = json_decode($edit_data->recurrence_rule, true) ?? [];
        
        return view('admin.live_classes.edit', compact('edit_data','accounts','courses'));
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
        return view('admin.live_classes.sort', ['list_items'=>$list_items]);
    }

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);
        if (!$cloned) return $this->errorResponse('Failed to clone item.');
        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.live_classes.edit', $cloned->id)
        ]);
    }
    public function storeSessions(Request $request, string $id)
    {
        $validated = $request->validate([
            'sessions' => 'required|array|min:1',
            'sessions.*.date' => 'required|date',
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i|after:sessions.*.start_time',
        ]);

        if ($this->service->storeSessions($id, $validated['sessions']))
        {
            return $this->successResponse('Sessions added successfully');
        }
        return $this->errorResponse('Failed to add sessions.');
    }
    public function updateRecurrence(UpdateLiveClassRecurrenceRequest $request, string $id)
    {
        // dd($request->validated());
        if($this->service->updateRecurrenceWithSessions($id, $request->validated())) {
            return $this->successResponse('Recurrence updated successfully. Upcoming sessions have been regenerated or removed based on the new recurrence rule.');
        }
        return $this->errorResponse('Failed to update recurrence');
    }
}