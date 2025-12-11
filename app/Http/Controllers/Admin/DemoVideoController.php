<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\DemoVideos\DemoVideoService;
use App\Services\Courses\CourseService;
use App\Services\CourseUnits\CourseUnitService;
use App\Http\Requests\DemoVideos\StoreDemoVideoRequest as StoreRequest;
use App\Http\Requests\DemoVideos\UpdateDemoVideoRequest as UpdateRequest;

class DemoVideoController extends AdminBaseController
{
    protected DemoVideoService $service;
    protected CourseService $courseService;
    protected CourseUnitService $courseUnitService;

    public function __construct(
        DemoVideoService $service,
        CourseService $courseService,
        CourseUnitService $courseUnitService
    ) {
        $this->service = $service;
        $this->courseService = $courseService;
        $this->courseUnitService = $courseUnitService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'provider', 'course_id']);
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

        return view('admin.demo_videos.index', [
            'page_title' => 'Demo Videos',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        $courses = $this->service->getCoursesForDropdown();
        $units = $this->service->getUnitsForDropdown();

        return view('admin.demo_videos.create', [
            'courses' => $courses,
            'units' => $units,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return $this->successResponse('Demo video created successfully');
    }

    public function show($id)
    {
        return view('admin.demo_videos.show', [
            'item' => $this->service->find($id),
        ]);
    }

    public function edit($id)
    {
        $demo_video = $this->service->find($id);
        $courses = $this->service->getCoursesForDropdown();
        $units = $this->service->getUnitsForDropdown($demo_video->course_id);

        return view('admin.demo_videos.edit', [
            'edit_data' => $demo_video,
            'courses' => $courses,
            'units' => $units,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Demo video updated successfully');
    }

    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete demo video');
        }
        return $this->successResponse('Demo video deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete demo videos');
        }
        return $this->successResponse('Selected demo videos deleted successfully');
    }

    public function cloneItem($id)
    {
        $demo_video = $this->service->find($id);

        $cloned = $this->service->clone($demo_video);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone demo video.');
        }

        return $this->successResponse('Demo video cloned successfully.', [
            'action'  => 'modal',
            'url'     => route('admin.demo-videos.edit', $cloned->id),
        ]);
    }

    public function sortView(Request $request)
    {
        return view('admin.demo_videos.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        if (!$result) {
            return $this->errorResponse('Failed to update sort order');
        }
        return $this->successResponse('Sort order updated successfully');
    }

    public function getUnitsForCourse(Request $request)
    {
        $courseId = $request->get('course_id');
        $units = $this->service->getUnitsForDropdown($courseId);
        
        return response()->json([
            'units' => $units
        ]);
    }
}
