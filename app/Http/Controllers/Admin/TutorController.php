<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Users\TutorService;
use App\Http\Requests\Tutors\StoreTutorRequest as StoreRequest;
use App\Http\Requests\Tutors\UpdateTutorRequest as UpdateRequest;

class TutorController extends AdminBaseController
{
    protected TutorService $service;

    public function __construct(TutorService $service)
    {
        $this->service = $service;
    }

    // List all tutors
    public function index(Request $request)
    {
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

        return view('admin.tutors.index', [
            'page_title' => 'Tutors',
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
        return view('admin.tutors.create', [
            'page_title' => 'Add Tutor',
        ]);
    }

    // Handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Tutor added successfully');
    }

    // Show edit form (AJAX modal)
    public function edit($id)
    {
        $tutor = $this->service->find($id);

        return view('admin.tutors.edit', [
            'page_title' => 'Edit Tutor',
            'edit_data' => $tutor,
        ]);
    }

    // Handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Tutor updated successfully');
    }

    // Show single tutor
    public function show($id)
    {
        return view('admin.tutors.show', [
            'page_title' => 'Tutor Details',
            'tutor' => $this->service->find($id),
        ]);
    }

    // Delete tutor
    public function destroy($id)
    {
        $result = $this->service->delete($id);
        if (!$result) {
            return $this->errorResponse('Tutor not found', null, 404);
        }

        return $this->successResponse('Tutor deleted successfully');
    }

    // Bulk delete tutors
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id'
        ]);

        try {
            $deletedCount = $this->service->bulkDelete($request->ids);
            return $this->successResponse("Successfully deleted {$deletedCount} tutor(s)");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Show sort view
    public function sortView()
    {
        $list_items = $this->service->getAll();
        
        return view('admin.tutors.sort', [
            'page_title' => 'Sort Tutors',
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
            $this->service->sortUpdate($request->order);
            return $this->successResponse('Sort order updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Clone tutor
    public function cloneItem($id)
    {
        try {
            $original = $this->service->find($id);
            if (!$original) {
                return $this->errorResponse('Tutor not found', null, 404);
            }

            $data = $original->toArray();
            unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
            
            // Make email and phone unique for cloned tutor
            $data['email'] = $data['email'] . '_cloned_' . time();
            $data['phone'] = $data['phone'] . '_cloned_' . time();
            $data['name'] = $data['name'] . ' (Cloned)';

            $cloned = $this->service->store($data);
            return $this->successResponse('Tutor cloned successfully', ['id' => $cloned->id]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
