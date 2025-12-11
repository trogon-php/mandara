<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\CourseUnits\CourseUnitService;
use App\Services\Courses\CourseService;
use App\Http\Requests\CourseUnits\StoreCourseUnitRequest as StoreRequest;
use App\Http\Requests\CourseUnits\UpdateCourseUnitRequest as UpdateRequest;

class CourseUnitController extends AdminBaseController
{
    protected CourseUnitService $service;
    protected CourseService $courseService;

    public function __construct(
        CourseUnitService $service,
        CourseService $courseService
    ) {
        $this->service = $service;
        $this->courseService = $courseService;
    }

    public function index($courseId, Request $request)
    {
        $course = $this->courseService->find($courseId);
        
        if (!$course) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course not found.');
        }

        $filters = $request->only(['type', 'access_type', 'status']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
            'course_id' => $courseId,
        ];

        if($filters || $searchParams['search']){
            $list_items = $this->service->getFilteredData($params);
        } else {
            $list_items = $this->service->getByCourse($courseId);
        }

        $filterConfig = $this->service->getFilterConfig();
        $searchConfig = $this->service->getSearchConfig();

        return view('admin.course_units.index', [
            'page_title' => 'Course Units - ' . $course->title,
            'course' => $course,
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $filterConfig,
            'searchConfig' => $searchConfig,
        ]);
    }

    public function create(Request $request, $courseId)
    {
        $course = $this->courseService->find($courseId);
        logger($course);

        $parentId = $request->get('parent_id');
        if($parentId) {
            $parentUnit = $this->service->find($parentId);
            $isParentPaid = $parentUnit->access_type == 'paid' ? true : false; 
        }

        return view('admin.course_units.create', [
            'page_title' => 'Add Course Unit',
            'course' => $course,
            'parentUnit' => $parentUnit ?? null,
            'isParentPaid' => $isParentPaid ?? null,
        ]);
    }

    public function store(StoreRequest $request, $courseId)
    {
        $course = $this->courseService->find($courseId);
        
        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course not found.',
            ], 404);
        }

        $data = $request->validated();
        $data['course_id'] = $courseId;

        $this->service->store($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Course unit added successfully',
        ]);
    }

    public function edit($courseId, $id)
    {
        $course = $this->courseService->find($courseId);
        $edit_data = $this->service->find($id);
        
        if (!$course || !$edit_data) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course or unit not found.');
        }

        $parentOptions = $this->service->getFlatListByCourse($courseId)
                                      ->where('id', '!=', $id);

        return view('admin.course_units.edit', [
            'page_title' => 'Edit Course Unit',
            'course' => $course,
            'edit_data' => $edit_data,
            'parentOptions' => $parentOptions,
        ]);
    }

    public function update(UpdateRequest $request, $courseId, $id)
    {
        $course = $this->courseService->find($courseId);
        $unit = $this->service->find($id);
        
        if (!$course || !$unit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course or unit not found.',
            ], 404);
        }

        $data = $request->validated();
        $data['course_id'] = $courseId;

        $this->service->update($id, $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Course unit updated successfully',
        ]);
    }

    // public function show($courseId, $id)
    // {
    //     $course = $this->courseService->find($courseId);
    //     $unit = $this->service->find($id);
        
    //     if (!$course || !$unit) {
    //         return redirect()->route('admin.courses.index')
    //                        ->with('error', 'Course or unit not found.');
    //     }

    //     return view('admin.course-units.show', [
    //         'page_title' => 'Course Unit Details',
    //         'course' => $course,
    //         'unit' => $unit,
    //     ]);
    // }

    public function sortView(Request $request, $courseId)
    {
        $course = $this->courseService->find($courseId);
        
        if (!$course) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course not found.');
        }

        // Check if we're sorting units within a specific section
        $parentId = $request->get('parent_id');
        
        if ($parentId) {
            // Sort units within a specific section
            $list_items = $this->service->getByCourse($courseId)->where('parent_id', $parentId);
            $parentSection = $this->service->find($parentId);
            $pageTitle = 'Sort Units in ' . ($parentSection ? $parentSection->title : 'Section');
        } else {
            // Sort sections (parent units)
            $list_items = $this->service->getByCourse($courseId)->whereNull('parent_id');
            $pageTitle = 'Sort Course Sections';
        }
        // dd($list_items);
        return view('admin.course_units.sort', [
            'page_title' => $pageTitle,
            'course' => $course,
            'list_items' => $list_items,
        ]);
    }

    public function sortUpdate(Request $request, $courseId)
    {
        $this->service->sortUpdate($request->order);

        return response()->json([
            'status' => 'success',
            'message' => 'Sort order updated successfully',
        ]);
    }

    public function destroy($courseId, $id)
    {
        $this->service->delete($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Course unit deleted successfully',
        ]);
    }

    public function bulkDelete(Request $request, $courseId)
    {
        $this->service->bulkDelete($request->ids);

        return response()->json([
            'status' => 'success',
            'message' => 'Selected course units deleted successfully',
        ]);
    }

    public function cloneItem($courseId, $id)
    {
        $course = $this->courseService->find($courseId);
        $unit = $this->service->find($id);
        
        if (!$course || !$unit) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course or unit not found.');
        }

        $clonedData = $unit->toArray();
        unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);
        $clonedData['title'] = $unit->title . ' (Copy)';
        $clonedData['slug'] = $unit->slug . '-copy';
        $clonedData['course_id'] = $courseId;

        $this->service->store($clonedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Course unit cloned successfully',
        ]);
    }
}


