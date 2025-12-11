<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\CourseMaterials\CourseMaterialService;
use App\Services\Courses\CourseService;
use App\Services\CourseUnits\CourseUnitService;
use App\Http\Requests\CourseMaterials\StoreCourseMaterialRequest as StoreRequest;
use App\Http\Requests\CourseMaterials\UpdateCourseMaterialRequest as UpdateRequest;

class CourseMaterialController extends AdminBaseController
{
    protected CourseMaterialService $service;
    protected CourseService $courseService;
    protected CourseUnitService $courseUnitService;

    public function __construct(
        CourseMaterialService $service,
        CourseService $courseService,
        CourseUnitService $courseUnitService
    ) {
        $this->service = $service;
        $this->courseService = $courseService;
        $this->courseUnitService = $courseUnitService;

        $this->middleware('can:course-materials/index')->only('index');
        $this->middleware('can:course-materials/create')->only(['create', 'store', 'cloneItem']);
        $this->middleware('can:course-materials/edit')->only(['edit', 'update', 'sortView', 'sortUpdate']);
        $this->middleware('can:course-materials/delete')->only('destroy', 'bulkDelete');
    }

    public function index(Request $request, $courseId)
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

        return view('admin.course_materials.index', [
            'page_title' => 'Course Materials - ' . $course->title,
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
        $unitId = $request->get('unit_id');
        $unit = null;
        
        if ($unitId) {
            // Try to find as course unit first
            $unit = $this->courseUnitService->find($unitId);
            
            // If not found as course unit, it might be a section
            if (!$unit) {
                // For sections, we'll pass the section ID as unit_id for the form
                $unit = (object) ['id' => $unitId, 'is_section' => true];
            }
        }

        return view('admin.course_materials.create', [
            'page_title' => 'Add Course Material',
            'course' => $course,
            'unit' => $unit,
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
            'message' => 'Course material added successfully',
        ]);
    }

    public function edit($courseId, $id)
    {
        $course = $this->courseService->find($courseId);
        $edit_data = $this->service->find($id);
        
        if (!$course || !$edit_data) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course or material not found.');
        }

        $unitOptions = $this->courseUnitService->getFlatListByCourse($courseId);

        return view('admin.course_materials.edit', [
            'page_title' => 'Edit Course Material',
            'course' => $course,
            'edit_data' => $edit_data,
            'unitOptions' => $unitOptions,
        ]);
    }

    public function update(UpdateRequest $request, $courseId, $id)
    {
        $course = $this->courseService->find($courseId);
        $material = $this->service->find($id);
        
        if (!$course || !$material) {
            return response()->json([
                'status' => 'error',
                'message' => 'Course or material not found.',
            ], 404);
        }

        $data = $request->validated();
        $data['course_id'] = $courseId;

        $this->service->update($id, $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Course material updated successfully',
        ]);
    }

    public function show($courseId, $id)
    {
        $course = $this->courseService->find($courseId);
        $material = $this->service->find($id);
        
        if (!$course || !$material) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course or material not found.');
        }

        return view('admin.course_materials.show', [
            'page_title' => 'Course Material Details',
            'course' => $course,
            'material' => $material,
        ]);
    }

    public function sortView(Request $request, $courseId)
    {
        $course = $this->courseService->find($courseId);

        // Get unit_id from query parameter
        $unitId = $request->get('unit_id');
        
        if ($unitId) {
            // Filter materials by specific unit
            $list_items = $this->service->getByUnit($unitId);
            $unit = $this->courseUnitService->find($unitId);
        } else {
            // Get all materials for the course
            $list_items = $this->service->getByCourse($courseId);
            $unit = null;
        }

        return view('admin.course_materials.sort', [
            'page_title' => $unitId ? 'Sort Materials - ' . ($unit ? $unit->title : 'Unit') : 'Sort Course Materials',
            'course' => $course,
            'unit' => $unit,
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
            'message' => 'Course material deleted successfully',
        ]);
    }

    public function bulkDelete(Request $request, $courseId)
    {
        $this->service->bulkDelete($request->ids);

        return response()->json([
            'status' => 'success',
            'message' => 'Selected course materials deleted successfully',
        ]);
    }

    public function cloneItem($courseId, $id)
    {
        $course = $this->courseService->find($courseId);
        $material = $this->service->find($id);
        
        if (!$course || !$material) {
            return redirect()->route('admin.courses.index')
                           ->with('error', 'Course or material not found.');
        }

        $clonedData = $material->toArray();
        unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);
        $clonedData['title'] = $material->title . ' (Copy)';
        $clonedData['course_id'] = $courseId;

        $this->service->store($clonedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Course material cloned successfully',
        ]);
    }
}
