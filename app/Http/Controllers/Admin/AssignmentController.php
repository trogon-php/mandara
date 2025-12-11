<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Assignments\StoreAssignmentRequest as StoreRequest;
use App\Http\Requests\Assignments\UpdateAssignmentRequest as UpdateRequest;
use App\Services\Assignments\AssignmentService;
use App\Services\Courses\CourseService;
use Illuminate\Http\Request;

class AssignmentController extends AdminBaseController
{
    public function __construct(
        private AssignmentService $service,
        private CourseService $courseService
        ){}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'course_id']);
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
        // dd($params);

        $list_items = $this->service->getFilteredData($params);
        $searchConfig = $this->service->getSearchConfig();
        $filterConfig = $this->service->getFilterConfig();
        // dd($list_items);
        return view('admin.assignments.index', [
            'page_title' => 'Assignments',
            'list_items' => $list_items,
            'searchConfig' => $searchConfig,
            'filterConfig' => $filterConfig,
        ]);
    }

    public function create()
    {
        $courses = $this->courseService->getCoursesOptions();
        
        return view('admin.assignments.create',[
            'courses' => $courses,
        ]);
    }

    public function store(StoreRequest $request)
    {
        // dd($request->validated());
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }

    public function edit($id)
    {
        $assignment = $this->service->find($id);
        return view('admin.assignments.edit', [
            'edit_data' => $assignment,
            'courses' => $this->courseService->getCoursesOptions(),
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());
            return $this->successResponse('Assignment updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update assignment: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->service->delete($id);
    }

    public function bulkDelete(Request $request)
    {
        $this->service->bulkDelete($request->ids);
    }
}
