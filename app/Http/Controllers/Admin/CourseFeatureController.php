<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\CourseFeatures\CourseFeatureService;
use App\Services\Courses\CourseService;
use App\Http\Requests\CourseFeatures\StoreCourseFeatureRequest as StoreRequest;
use App\Http\Requests\CourseFeatures\UpdateCourseFeatureRequest as UpdateRequest;

class CourseFeatureController extends AdminBaseController
{
    protected CourseFeatureService $service;
    protected CourseService $courseService;

    public function __construct(
        CourseFeatureService $service,
        CourseService $courseService
    ) {
        $this->service = $service;
        $this->courseService = $courseService;

        $this->middleware('can:course-features/index')->only('index');
        $this->middleware('can:course-features/create')->only(['create', 'store', 'cloneItem']);
        $this->middleware('can:course-features/edit')->only(['edit', 'update', 'sortView', 'sortUpdate']);
        $this->middleware('can:course-features/delete')->only('destroy', 'bulkDelete');
    }

    // List all course features
    public function index(Request $request)
    {
        $filters = $request->only(['course_id']);
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
            'paginate' => true,
            'per_page' => $request->get('per_page', 15),
        ];

        $list_items = $this->service->getFilteredData($params);

        // Load course relationship for each feature
        $list_items->getCollection()->load('course');

        return view('admin.course-features.index', [
            'page_title' => 'Course Features',
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
        $courses = $this->courseService->getAll()->pluck('title', 'id');
        
        return view('admin.course-features.create', [
            'page_title' => 'Add Course Feature',
            'courses' => $courses,
        ]);
    }

    // Handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Course feature added successfully');
    }

    // Show edit form (AJAX modal)
    public function edit($id)
    {
        $courseFeature = $this->service->find($id);
        if (!$courseFeature) {
            return $this->errorResponse('Course feature not found', null, 404);
        }

        $courses = $this->courseService->getAll()->pluck('title', 'id');

        return view('admin.course-features.edit', [
            'page_title' => 'Edit Course Feature',
            'courseFeature' => $courseFeature,
            'courses' => $courses,
        ]);
    }

    // Handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $courseFeature = $this->service->update($id, $request->validated());
        if (!$courseFeature) {
            return $this->errorResponse('Course feature not found', null, 404);
        }

        return $this->successResponse('Course feature updated successfully');
    }

    // Show single course feature
    public function show($id)
    {
        $courseFeature = $this->service->find($id, ['course']);
        if (!$courseFeature) {
            return $this->errorResponse('Course feature not found', null, 404);
        }

        return view('admin.course-features.show', [
            'page_title' => 'Course Feature Details',
            'courseFeature' => $courseFeature,
        ]);
    }

    // Delete course feature
    public function destroy($id)
    {
        $deleted = $this->service->delete($id);
        if (!$deleted) {
            return $this->errorResponse('Course feature not found', null, 404);
        }

        return $this->successResponse('Course feature deleted successfully');
    }

    // Bulk delete course features
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return $this->errorResponse('No course features selected');
        }

        $deletedCount = $this->service->bulkDelete($ids);
        return $this->successResponse("{$deletedCount} course features deleted successfully");
    }

    // Show sort view
    public function sortView()
    {
        $courseFeatures = $this->service->getAll();
        return view('admin.course-features.sort', [
            'page_title' => 'Sort Course Features',
            'courseFeatures' => $courseFeatures,
        ]);
    }

    // Update sort order
    public function sortUpdate(Request $request)
    {
        $order = $request->input('order', []);
        if (empty($order)) {
            return $this->errorResponse('No order data provided');
        }

        $this->service->sortUpdate($order);
        return $this->successResponse('Sort order updated successfully');
    }

    // Clone course feature
    public function cloneItem($id)
    {
        $courseFeature = $this->service->find($id);
        if (!$courseFeature) {
            return $this->errorResponse('Course feature not found', null, 404);
        }

        $data = $courseFeature->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);
        $data['title'] = $data['title'] . ' (Copy)';

        $this->service->store($data);
        return $this->successResponse('Course feature cloned successfully');
    }
}
