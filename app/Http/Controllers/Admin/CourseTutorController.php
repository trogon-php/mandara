<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\CourseTutors\CourseTutorService;
use App\Services\Courses\CourseService;
use App\Services\Users\UserService;
use App\Http\Requests\CourseTutors\StoreCourseTutorRequest as StoreRequest;
use App\Http\Requests\CourseTutors\UpdateCourseTutorRequest as UpdateRequest;

class CourseTutorController extends AdminBaseController
{
    protected CourseTutorService $service;
    protected CourseService $courseService;
    protected UserService $userService;

    public function __construct(
        CourseTutorService $service,
        CourseService $courseService,
        UserService $userService
    ) {
        $this->service = $service;
        $this->courseService = $courseService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['course_id', 'role', 'user_id']);
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

        return view('admin.course-tutors.index', [
            'page_title' => 'Course Tutors',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        $courses = $this->courseService->getAll()->pluck('title', 'id');
        $tutors = $this->service->getAvailableTutors()->pluck('name', 'id');
        
        return view('admin.course-tutors.create', [
            'courses' => $courses,
            'tutors' => $tutors,
            'selectedTutorId' => request()->get('tutor_id'),
        ]);
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return $this->successResponse('Course tutor added successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function edit($id)
    {
        $courseTutor = $this->service->find($id, ['course', 'user']);
        if (!$courseTutor) {
            return $this->errorResponse('Course tutor not found', null, 404);
        }

        $courses = $this->courseService->getAll()->pluck('title', 'id');
        $tutors = $this->service->getAvailableTutors()->pluck('name', 'id');
        
        return view('admin.course-tutors.edit', [
            'edit_data' => $courseTutor,
            'courses' => $courses,
            'tutors' => $tutors,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $courseTutor = $this->service->update($id, $request->validated());
            if (!$courseTutor) {
                return $this->errorResponse('Course tutor not found', null, 404);
            }
            return $this->successResponse('Course tutor updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->service->delete($id);
            if (!$result) {
                return $this->errorResponse('Course tutor not found', null, 404);
            }
            return $this->successResponse('Course tutor deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:course_tutors,id'
        ]);

        try {
            $deletedCount = $this->service->bulkDelete($request->ids);
            return $this->successResponse("Successfully deleted {$deletedCount} course tutor(s)");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function sortView()
    {
        $list_items = $this->service->getAll();
        
        return view('admin.course-tutors.sort', [
            'page_title' => 'Sort Course Tutors',
            'list_items' => $list_items,
        ]);
    }

    public function sortUpdate(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:course_tutors,id'
        ]);

        try {
            $this->service->sortUpdate($request->order);
            return $this->successResponse('Sort order updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function cloneItem($id)
    {
        try {
            $original = $this->service->find($id, ['course', 'user']);
            if (!$original) {
                return $this->errorResponse('Course tutor not found', null, 404);
            }

            $data = $original->toArray();
            unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
            $data['sort_order'] = $this->service->model->max('sort_order') + 1;

            $cloned = $this->service->store($data);
            return $this->successResponse('Course tutor cloned successfully', ['id' => $cloned->id]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get tutors for a specific course (AJAX)
     */
    public function getTutorsForCourse(Request $request, $courseId)
    {
        try {
            $tutors = $this->service->getTutorsForCourse($courseId);
            return $this->successResponse('Tutors retrieved successfully', $tutors);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get courses for a specific tutor (AJAX)
     */
    public function getCoursesForTutor(Request $request, $userId)
    {
        try {
            $courses = $this->service->getCoursesForTutor($userId);
            return $this->successResponse('Courses retrieved successfully', $courses);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
