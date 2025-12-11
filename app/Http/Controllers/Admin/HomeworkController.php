<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Homework\StoreHomeworkRequest as StoreRequest;
use App\Http\Requests\Homework\UpdateHomeworkRequest as UpdateRequest;
use App\Services\Courses\CourseService;
use App\Services\Homework\HomeworkService;
use Illuminate\Http\Request;

class HomeworkController extends AdminBaseController
{
    public function __construct(
        private HomeworkService $service,
        private CourseService $courseService
        ) {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['status','course_id']));
        $searchParams = ['search' => $request->get('search')];

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);

        return view('admin.homeworks.index', [
            'page_title' => 'Homework List',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        $courses = $this->courseService->getCoursesOptions();
        return view('admin.homeworks.create', [
            'courses' => $courses,
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
        return view('admin.homeworks.edit', [
            'edit_data' => $edit_data,
            'courses' => $this->courseService->getCoursesOptions(),
        ]);
    }

    public function update(UpdateRequest $request, string $id)
    {
        // dd($request->validated());
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

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);
        if (!$cloned) return $this->errorResponse('Failed to clone item.');
        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.homeworks.edit', $cloned->id)
        ]);
    }
}