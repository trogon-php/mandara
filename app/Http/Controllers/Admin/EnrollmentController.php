<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Enrollments\EnrollmentService;
use App\Services\Users\StudentService;
use App\Services\Courses\CourseService;
use App\Http\Requests\Enrollments\StoreEnrollmentRequest as StoreRequest;
use App\Http\Requests\Enrollments\UpdateEnrollmentRequest as UpdateRequest;

class EnrollmentController extends AdminBaseController
{
    protected EnrollmentService $service;
    protected StudentService $studentService;
    protected CourseService $courseService;

    public function __construct(
        EnrollmentService $service,
        StudentService $studentService,
        CourseService $courseService
    ) {
        $this->service = $service;
        $this->studentService = $studentService;
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'type', 'course_id']);
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

        $list_items = $this->service->getFilteredData($params);

        return view('admin.enrollments.index', [
            'page_title' => 'Enrollments',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        if(request('student_id')) {
            $student = $this->studentService->find(request('student_id'));
        }
        // dd($student);
        $courses = $this->courseService->getCoursesOptions();

        return view('admin.enrollments.create', [
            'page_title' => 'Add Enrollment',
            'courses' => $courses,
            'student' => $student ?? null,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Enrollment added successfully');
    }

    public function edit($id)
    {
        $enrollment = $this->service->find($id);
        $users = $this->studentService->getStudentsOptions();
        $courses = $this->courseService->getCoursesOptions();

        return view('admin.enrollments.edit', [
            'page_title' => 'Edit Enrollment',
            'edit_data' => $enrollment,
            'users' => $users,
            'courses' => $courses,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Enrollment updated successfully');
    }


    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse('Enrollment deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $this->service->bulkDelete($ids);
        return $this->successResponse('Selected enrollments deleted successfully');
    }
}

