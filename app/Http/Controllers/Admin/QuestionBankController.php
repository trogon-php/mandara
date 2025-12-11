<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\QuestionBanks\QuestionBankService;
use App\Http\Requests\QuestionBanks\StoreQuestionBankRequest;
use App\Http\Requests\QuestionBanks\UpdateQuestionBankRequest;
use App\Models\Course;
use App\Services\Courses\CourseService;
use App\Services\CourseUnits\CourseUnitService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionBankController extends AdminBaseController
{
    public function __construct(
        private QuestionBankService $service,
        private CourseService $courseService,
        private CourseUnitService $courseUnitService
        ) {}

    /**
     * Display a listing of question banks
     */
    public function index(Request $request)
    {
        $filters = array_filter($request->only(['status', 'course_id', 'unit_id', 'parent_id']));
        $searchParams = ['search' => $request->get('search')];

        if($filters) {
            // dd($filters);
            $list_items = $this->service->getFilteredData([
                'search' => $searchParams['search'], 
                'filters' => $filters
            ]);
        } else {

            $list_items = $this->service->getFlatList();

        }
        // dd($list_items);
        return view('admin.question_banks.index', [
            'page_title' => 'Question Banks',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    /**
     * Show the form for creating a new question bank
     */
    public function create()
    {
        $parentQuestionBanks = $this->service->getFlatList()->pluck('indented_title', 'id');
        $courseOptions = $this->courseService->getCoursesOptions();

        return view('admin.question_banks.create', [
            'page_title' => 'Create Question Bank',
            'parentQuestionBanks' => $parentQuestionBanks,
            'courseOptions' => $courseOptions
        ]);
    }

    /**
     * Store a newly created question bank
     */
    public function store(StoreQuestionBankRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return $this->successResponse('Question bank created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create question bank: ' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified question bank
     */
    public function edit(string $id)
    {
        $edit_data = $this->service->find($id);
        $parentQuestionBanks = $this->service->getFlatList()->pluck('indented_title', 'id');
        $courseOptions = $this->courseService->getCoursesOptions();

        if (!$edit_data) {
            return $this->redirectWithError('admin.question-banks.index', 'Question bank not found');
        }

        return view('admin.question_banks.edit', [
            'page_title' => 'Edit Question Bank',
            'edit_data' => $edit_data,
            'parentQuestionBanks' => $parentQuestionBanks,
            'courseOptions' => $courseOptions
        ]);
    }

    /**
     * Update the specified question bank
     */
    public function update(UpdateQuestionBankRequest $request, string $id)
    {
        try {
            $result = $this->service->update($id, $request->validated());
            
            if (!$result) {
                return $this->errorResponse('Question bank not found', null, 404);
            }

            return $this->successResponse('Question bank updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update question bank: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question bank
     */
    public function destroy(string $id)
    {
        try {
            if (!$this->service->delete($id)) {
                return $this->errorResponse('Failed to delete question bank', null, 404);
            }

            return $this->successResponse('Question bank deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete question bank: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete question banks
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:question_banks,id'
        ]);

        try {
            $deletedCount = $this->service->bulkDelete($request->ids);
            
            if ($deletedCount === 0) {
                return $this->errorResponse('No question banks were deleted');
            }

            return $this->successResponse("Successfully deleted {$deletedCount} question bank(s)");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete question banks: ' . $e->getMessage());
        }
    }

    /**
     * Update sort order
     */
    public function sortUpdate(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:question_banks,id'
        ]);

        try {
            $result = $this->service->sortUpdate($request->order);
            
            if (!$result) {
                return $this->errorResponse('Failed to update sort order');
            }

            return $this->successResponse('Sort order updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update sort order: ' . $e->getMessage());
        }
    }

    /**
     * Get question banks by course (AJAX)
     */
    public function getByCourse(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,id'
        ]);

        try {
            $questionBanks = $this->service->getByCourse($request->course_id);
            return $this->successResponse('Question banks retrieved successfully', $questionBanks);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve question banks: ' . $e->getMessage());
        }
    }

    /**
     * Get question banks by unit (AJAX)
     */
    public function getByUnit(Request $request): JsonResponse
    {
        $request->validate([
            'unit_id' => 'required|integer|exists:course_units,id'
        ]);

        try {
            $questionBanks = $this->service->getByUnit($request->unit_id);
            return $this->successResponse('Question banks retrieved successfully', $questionBanks);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve question banks: ' . $e->getMessage());
        }
    }

    /**
     * Get tree structure (AJAX)
     */
    public function getTree(Request $request): JsonResponse
    {
        $courseId = $request->get('course_id');
        
        try {
            $tree = $this->service->getTreeStructure($courseId);
            return $this->successResponse('Tree structure retrieved successfully', $tree);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve tree structure: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $questionBank = $this->service->find($id);
            
            if (!$questionBank) {
                return $this->errorResponse('Question bank not found');
            }

            $questionBank->update(['status' => $request->status]);
            
            return $this->successResponse('Status updated successfully', [
                'id' => $questionBank->id,
                'status' => $questionBank->status
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Get units by course (AJAX)
     */
    public function getUnits(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|integer|exists:courses,id'
        ]);

        try {
            $units = \App\Models\CourseUnit::where('course_id', $request->course_id)
                ->active()
                ->select('id', 'title')
                ->get();
            
            return $this->successResponse('Units retrieved successfully', $units);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve units: ' . $e->getMessage());
        }
    }

    /**
     * Clone question bank
     */
    public function cloneItem(string $id)
    {
        try {
            $originalBank = $this->service->find($id);
            
            if (!$originalBank) {
                return $this->redirectWithError('admin.question-banks.index', 'Question bank not found');
            }

            $clonedData = $originalBank->toArray();
            unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);
            $clonedData['title'] = $originalBank->title . ' (Copy)';
            $clonedData['status'] = 'draft';

            $this->service->store($clonedData);
            
            return $this->redirectWithSuccess('admin.question-banks.index', 'Question bank cloned successfully');
        } catch (\Exception $e) {
            return $this->redirectBackWithError('Failed to clone question bank: ' . $e->getMessage());
        }
    }

    /**
     * Sort view
     */
    public function sortView()
    {
        $questionBanks = $this->service->getAllForSelect();
        
        return view('admin.question_banks.sort', [
            'page_title' => 'Sort Question Banks',
            'question_banks' => $questionBanks,
        ]);
    }
}
