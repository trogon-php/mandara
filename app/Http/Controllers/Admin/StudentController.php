<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Users\{
    StudentService,
    UserService,
    UserMetaService
};
use App\Http\Requests\Students\{
    StoreStudentRequest as StoreRequest,
    UpdateStudentRequest as UpdateRequest
};

class StudentController extends AdminBaseController
{

    public function __construct(
        protected StudentService $service, 
        protected UserService $userService, 
        protected UserMetaService $userMetaService)
    {
    }

    // List all students
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'course_id']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Handle date range filter
        if ($request->has('date_from') || $request->has('date_to')) {
            $dateRange = [];
            if ($request->filled('date_from')) {
                $dateRange['from'] = $request->get('date_from');
            }
            if ($request->filled('date_to')) {
                $dateRange['to'] = $request->get('date_to');
            }
            if (!empty($dateRange)) {
                $filters['date_range'] = $dateRange;
            }
        }

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
            'paginate' => true,
            'per_page' => $request->get('per_page', 25), // Default 25 students per page
        ];

        $list_items = $this->service->getFilteredData($params);

        return view('admin.students.index', [
            'page_title' => 'Students',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    // Show add form (AJAX modal)
    public function create()
    {
        $userMetaOptions = $this->userMetaService->getFieldOptions();
        return view('admin.students.create', [
            'page_title' => 'Add Student',
            'userMetaOptions' => $userMetaOptions,
        ]);
    }

    // Handle add form submission
    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();
        
        // Use UserService with meta data support
        $this->userService->storeWithMeta($validatedData);
        
        return $this->successResponse('Student added successfully');
    }

    // Show edit form (AJAX modal)
    public function edit($id)
    {
        $student = $this->service->find($id);

        return view('admin.students.edit', [
            'page_title' => 'Edit Student',
            'edit_data' => $student,
        ]);
    }

    // Handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Student updated successfully');
    }

    // Show single student
    public function show($id)
    {
        return view('admin.students.show', [
            'page_title' => 'Student Details',
            'student' => $this->service->find($id),
        ]);
    }

    // Delete student
    public function destroy($id)
    {
        $result = $this->service->delete($id);
        if (!$result) {
            return $this->errorResponse('Student not found', null, 404);
        }

        return $this->successResponse('Student deleted successfully');
    }

    // Bulk delete students
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id'
        ]);

        try {
            $deletedCount = $this->service->bulkDelete($request->ids);
            return $this->successResponse("Successfully deleted {$deletedCount} student(s)");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Show sort view
    public function sortView()
    {
        $list_items = $this->service->getAll();
        
        return view('admin.students.sort', [
            'page_title' => 'Sort Students',
            'list_items' => $list_items,
        ]);
    }

    // Handle sort update
    public function sortUpdate(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:users,id'
        ]);

        try {
            $this->service->updateSortOrder($request->order);
            return $this->successResponse('Students sorted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getSelect2AjaxOptions(Request $request)
    {
        $params = [
            'search' => $request->get('search', ''),
            'page' => $request->get('page', 1),
            'per_page' => $request->get('per_page', 15)
        ];
        
        $students = $this->service->getSelect2AjaxOptions($params);
        
        $studentOptions = $students->map(function ($user) {
            $phone = $user->phone ? " [+{$user->country_code} {$user->phone}]" : '';
            $label = $user->name . $phone;

            return ['id' => $user->id, 'label' => $label];
        });
        return response()->json([
            'data' => $studentOptions,
            'pagination' => [
                'more' => $students->hasMorePages()
            ]
        ]);
    }
}