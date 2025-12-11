<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Documents\{
    StoreDocumentRequest as StoreRequest, 
    UpdateDocumentRequest as UpdateRequest
};
use App\Services\{
    Documents\DocumentService, 
    CourseMaterials\CourseMaterialService, 
    CourseUnits\CourseUnitService,
    Courses\CourseService
};
use Illuminate\Http\Request;

class DocumentController extends AdminBaseController
{
    public function __construct(
        private DocumentService $service,
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

        return view('admin.documents.index', [
            'page_title' => 'Documents',
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
        $unitId = $request->get('unit_id', 1);
        $courseId = $request->get('course_id', 1);

        if($unitId){
            $unit = $this->unitService->find($unitId);
            $isParentPaid = $unit->access_type == 'paid' ? true : false;
        }

        if($courseId){
            $course = $this->courseService->find($courseId);
        }

        return view('admin.documents.create', [
            'page_title' => 'Add Document',
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
            'message' => 'Document added successfully',
        ]);
    }

    public function edit($id)
    {
        $edit_data = $this->service->find($id,['material.courseUnit']);
        $unit = $edit_data->material->courseUnit;
        $isParentPaid = $unit->access_type == 'paid' ? true : false;
        
        return view('admin.documents.edit', [
            'page_title' => 'Edit Document - ' . $edit_data->material->title,
            'edit_data' => $edit_data,
            'isParentPaid' => $isParentPaid ?? null,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {

        $this->service->update($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Document updated successfully',
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

    public function destroy($id)
    {
        $document = $this->service->find($id);
        
        if (!$document) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        // Get course ID from the document's material before deleting
        $courseId = $document->material->course_id ?? null;
        
        $this->service->delete($id);

        if ($courseId) {
            return redirect()->route('admin.course-content.index', ['course' => $courseId])
                           ->with('success', 'Document deleted successfully');
        }

        return redirect()->back()->with('success', 'Document deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete items');
        }
        return $this->successResponse('Selected items deleted successfully');
    }

    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        if (!$result) return $this->errorResponse('Failed to update sort order');
        return $this->successResponse('Sort order updated successfully');
    }

    public function sortView()
    {
        $list_items = $this->service->getAll();
        return view('admin.documents.sort', ['list_items'=>$list_items]);
    }

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);
        if (!$cloned) return $this->errorResponse('Failed to clone item.');
        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.documents.edit', $cloned->id)
        ]);
    }
}