<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;

use App\Services\{
    Videos\VideoService, 
    CourseMaterials\CourseMaterialService, 
    CourseUnits\CourseUnitService,
    Courses\CourseService
};

use App\Http\Requests\Videos\{
    StoreVideoRequest as StoreRequest, 
    UpdateVideoRequest as UpdateRequest
};

class VideoController extends AdminBaseController
{
    public function __construct(
        private VideoService $service,
        private CourseService $courseService,
        private CourseMaterialService $materialService,
        private CourseUnitService $unitService
    ) {}

    public function index(Request $request)
    {
        $unitId = $request->get('unit_id');
        $courseId = $request->get('course_id');

        if($unitId){
            $unit = $this->unitService->find($unitId);
        }

        if($courseId){
            $course = $this->courseService->find($courseId);
        }

        $filters = $request->only(['status']);
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

        return view('admin.videos.index', [
            'page_title' => 'Videos',
            'list_items' => $list_items,
            'unit' => $unit ?? null,
            'course' => $course ?? null,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create(Request $request)
    {
        $courseId = $request->get('course_id');
        $unitId = $request->get('unit_id');

        if($unitId){
            $unit = $this->unitService->find($unitId);
            $isParentPaid = $unit->access_type == 'paid' ? true : false;
        }

        if($courseId){
            $course = $this->courseService->find($courseId);
        }

        return view('admin.videos.create', [
            'page_title' => 'Add Video',
            'unit' => $unit ?? null,
            'course' => $course ?? null,
            'isParentPaid' => $isParentPaid ?? null,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $this->service->store($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Video added successfully',
        ]);
    }

    public function edit(Request $request, $courseId)
    {
        $materialId = $request->get('material_id');
        $material = $this->materialService->find($materialId,['courseUnit']);
        $video = $material?->video;
        $isParentPaid = $material->courseUnit->access_type == 'paid' ? true : false;
        
        if (!$material || !$video) {
            return redirect()->route('admin.course-materials.index', $courseId)
                           ->with('error', 'Material or video not found.');
        }

        return view('admin.videos.edit', [
            'page_title' => 'Edit Video - ' . $material->title,
            'video' => $video,
            'material' => $material,
            'course_id' => $courseId,
            'isParentPaid' => $isParentPaid ?? null,
        ]);
    }

    public function update(UpdateRequest $request, $materialId)
    {
        $material = $this->materialService->find($materialId);
        $video = $material?->video;
        
        if (!$material || !$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Material or video not found.',
            ], 404);
        }

        $data = $request->validated();
        $data['material_id'] = $materialId;

        $this->service->update($video->id, $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Video updated successfully',
        ]);
    }

    public function show($courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        $video = $material?->video;
        
        if (!$material || !$video) {
            return redirect()->route('admin.course-materials.index', $courseId)
                           ->with('error', 'Material or video not found.');
        }

        return view('admin.videos.show', [
            'page_title' => 'Video Details - ' . $material->title,
            'video' => $video,
            'material' => $material,
            'course_id' => $courseId,
        ]);
    }

    public function destroy($courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        $video = $material?->video;
        
        if (!$material || !$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Material or video not found.',
            ], 404);
        }

        $this->service->delete($video->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Video deleted successfully',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:videos,id'
        ]);

        $deletedCount = $this->service->bulkDelete($request->ids);

        return response()->json([
            'status' => 'success',
            'message' => "Successfully deleted {$deletedCount} video(s)",
            'deleted_count' => $deletedCount
        ]);
    }
}
