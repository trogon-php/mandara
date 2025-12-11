<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Notes\NoteService;
use App\Services\CourseMaterials\CourseMaterialService;
use App\Http\Requests\Notes\StoreNoteRequest as StoreRequest;
use App\Http\Requests\Notes\UpdateNoteRequest as UpdateRequest;

class NoteController extends AdminBaseController
{
    protected NoteService $service;
    protected CourseMaterialService $materialService;

    public function __construct(
        NoteService $service,
        CourseMaterialService $materialService
    ) {
        $this->service = $service;
        $this->materialService = $materialService;

        $this->middleware('can:notes/index')->only('index');
        $this->middleware('can:notes/create')->only(['create', 'store']);
        $this->middleware('can:notes/edit')->only(['edit', 'update']);
        $this->middleware('can:notes/delete')->only('destroy');
    }

    public function index(Request $request, $courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        
        if (!$material) {
            return redirect()->route('admin.course-materials.index', $courseId)
                           ->with('error', 'Material not found.');
        }

        $note = $material->note;

        return view('admin.notes.show', [
            'page_title' => 'Note Details - ' . $material->title,
            'note' => $note,
            'material' => $material,
            'course_id' => $courseId,
        ]);
    }

    public function create($courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        
        if (!$material) {
            return redirect()->route('admin.course-materials.index', $courseId)
                           ->with('error', 'Material not found.');
        }

        // Check if material already has a note
        if ($material->hasNote()) {
            return redirect()->route('admin.course-materials.notes.index', [$courseId, $materialId])
                           ->with('error', 'This material already has a note.');
        }

        return view('admin.notes.create', [
            'page_title' => 'Add Note - ' . $material->title,
            'material' => $material,
            'course_id' => $courseId,
        ]);
    }

    public function store(StoreRequest $request, $courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        
        if (!$material) {
            return response()->json([
                'status' => 'error',
                'message' => 'Material not found.',
            ], 404);
        }

        // Check if material already has a note
        if ($material->hasNote()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This material already has a note.',
            ], 400);
        }

        $data = $request->validated();
        $data['material_id'] = $materialId;

        $this->service->store($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Note added successfully',
        ]);
    }

    public function edit($courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        $note = $material?->note;
        
        if (!$material || !$note) {
            return redirect()->route('admin.course-materials.index', $courseId)
                           ->with('error', 'Material or note not found.');
        }

        return view('admin.notes.edit', [
            'page_title' => 'Edit Note - ' . $material->title,
            'note' => $note,
            'material' => $material,
            'course_id' => $courseId,
        ]);
    }

    public function update(UpdateRequest $request, $courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        $note = $material?->note;
        
        if (!$material || !$note) {
            return response()->json([
                'status' => 'error',
                'message' => 'Material or note not found.',
            ], 404);
        }

        $data = $request->validated();
        $data['material_id'] = $materialId;

        $this->service->update($note->id, $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Note updated successfully',
        ]);
    }

    public function show($courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        $note = $material?->note;
        
        if (!$material || !$note) {
            return redirect()->route('admin.course-materials.index', $courseId)
                           ->with('error', 'Material or note not found.');
        }

        return view('admin.notes.show', [
            'page_title' => 'Note Details - ' . $material->title,
            'note' => $note,
            'material' => $material,
            'course_id' => $courseId,
        ]);
    }

    public function destroy($courseId, $materialId)
    {
        $material = $this->materialService->find($materialId);
        $note = $material?->note;
        
        if (!$material || !$note) {
            return response()->json([
                'status' => 'error',
                'message' => 'Material or note not found.',
            ], 404);
        }

        $this->service->delete($note->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Note deleted successfully',
        ]);
    }
}
