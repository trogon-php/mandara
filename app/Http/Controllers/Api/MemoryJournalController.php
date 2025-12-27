<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\MemoryJournals\AppMemoryJournalResource;
use App\Services\MemoryJournals\MemoryJournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MemoryJournalController extends BaseApiController
{
    public function __construct(
        protected MemoryJournalService $memoryJournalService
    ) {}

    /**
     * Get paginated memory journals for authenticated user
     */
    public function myMemories(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $perPage = $request->get('per_page', 10);
        
        $journals = $this->memoryJournalService->getUserMemoryJournals($user->id, $perPage);
        $journals = AppMemoryJournalResource::collection($journals);

        return $this->respondPaginated($journals, 'Memory journals retrieved successfully');
    }

    /**
     * Get single memory journal
     */
    public function show($id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $journal = $this->memoryJournalService->getMemoryJournalForApi($id, $user->id);
        
        if (!$journal) {
            return $this->respondError('Memory journal not found', [], 404);
        }

        $journal = AppMemoryJournalResource::make($journal);

        return $this->respondSuccess($journal, 'Memory journal retrieved successfully');
    }

    /**
     * Store a new memory journal
     */
    public function store(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $data = $request->validate([
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'content' => 'required|string',
        ]);

        $data['user_id'] = $user->id;
        $journal = $this->memoryJournalService->createUserMemoryJournal($data);
        $journal = AppMemoryJournalResource::make($journal);

        return $this->respondSuccess($journal, 'Memory journal created successfully', 201);
    }

    /**
     * Update a memory journal
     */
    public function update(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }
        $data = $request->validate([
            'date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'content' => 'nullable|string',
        ]);
        
        // dd($data);
        $journal = $this->memoryJournalService->updateUserMemoryJournal($id, $user->id, $data);
        
        if (!$journal) {
            return $this->respondError('Memory journal not found or you do not have permission to update it', 404);
        }

        $journal = AppMemoryJournalResource::make($journal);

        return $this->respondSuccess($journal, 'Memory journal updated successfully');
    }

    /**
     * Delete a memory journal
     */
    public function destroy($id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $result = $this->memoryJournalService->deleteUserMemoryJournal($id, $user->id);

        if (!$result) {
            return $this->respondError('Memory journal not found or you do not have permission to delete it',404);
        }

        return $this->respondSuccess([], 'Memory journal deleted successfully');
    }

    /**
     * Get memory journals by date range
     */
    public function getByDateRange(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $journals = $this->memoryJournalService->getMemoryJournalsByDateRange(
            $user->id,
            $data['start_date'],
            $data['end_date']
        );

        $journals = AppMemoryJournalResource::collection($journals);

        return $this->respondSuccess($journals, 'Memory journals retrieved successfully');
    }
}