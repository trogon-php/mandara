<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\CourseReviews\CourseReviewService;
use App\Services\Courses\CourseService;
use App\Services\Users\UserService;
use App\Http\Requests\CourseReviews\StoreCourseReviewRequest as StoreRequest;
use App\Http\Requests\CourseReviews\UpdateCourseReviewRequest as UpdateRequest;

class CourseReviewController extends AdminBaseController
{
    protected CourseReviewService $service;
    protected CourseService $courseService;
    protected UserService $userService;

    public function __construct(
        CourseReviewService $service,
        CourseService $courseService,
        UserService $userService
    ) {
        $this->service = $service;
        $this->courseService = $courseService;
        $this->userService = $userService;

        $this->middleware('can:course-reviews/index')->only('index');
        $this->middleware('can:course-reviews/create')->only(['create', 'store', 'cloneItem']);
        $this->middleware('can:course-reviews/edit')->only(['edit', 'update', 'sortView', 'sortUpdate', 'approve', 'reject']);
        $this->middleware('can:course-reviews/delete')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'rating', 'course_id', 'date_from', 'date_to']);
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

        return view('admin.course-reviews.index', [
            'page_title' => 'Course Reviews',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        $courses = $this->courseService->getIdTitle();
        $users = $this->userService->getIdTitle('name');

        return view('admin.course-reviews.create', [
            'page_title' => 'Add Course Review',
            'courses' => $courses,
            'users' => $users,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Course review added successfully',
        ]);
    }

    public function edit($id)
    {
        $edit_data = $this->service->find($id);
        $courses = $this->courseService->getIdTitle();
        $users = $this->userService->getIdTitle('name');

        return view('admin.course-reviews.edit', [
            'page_title' => 'Edit Course Review',
            'edit_data' => $edit_data,
            'courses' => $courses,
            'users' => $users,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Course review updated successfully',
        ]);
    }

    public function show($id)
    {
        return view('admin.course-reviews.show', [
            'page_title' => 'Course Review Details',
            'review' => $this->service->find($id),
        ]);
    }

    public function sortView(Request $request)
    {
        return view('admin.course-reviews.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    public function sortUpdate(Request $request)
    {
        $this->service->sortUpdate($request->order);

        return response()->json([
            'status' => 'success',
            'message' => 'Sort order updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Course review deleted successfully',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $this->service->bulkDelete($request->ids);

        return response()->json([
            'status' => 'success',
            'message' => 'Selected course reviews deleted successfully',
        ]);
    }

    public function cloneItem($id)
    {
        $review = $this->service->find($id);
        $cloned = $this->service->clone($review);

        if (!$cloned) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clone course review.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Course review cloned successfully.',
            'action' => 'modal',
            'url' => route('admin.course-reviews.edit', $cloned->id),
        ]);
    }

    public function approve($id)
    {
        $this->service->approveReview($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Course review approved successfully',
        ]);
    }

    public function reject($id)
    {
        $this->service->rejectReview($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Course review rejected successfully',
        ]);
    }

    public function bulkApprove(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->service->approveReview($id);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Selected course reviews approved successfully',
        ]);
    }

    public function bulkReject(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->service->rejectReview($id);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Selected course reviews rejected successfully',
        ]);
    }
}
