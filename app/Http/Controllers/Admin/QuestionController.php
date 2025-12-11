<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use App\Services\Questions\QuestionService;
use App\Services\QuestionBanks\QuestionBankService;
use App\Services\Courses\CourseService;
use App\Services\CourseUnits\CourseUnitService;
use Illuminate\Http\Request;

class QuestionController extends AdminBaseController
{
    public function __construct(
        private QuestionService $service,
        private QuestionBankService $questionBankService,
        private CourseService $courseService,
        private CourseUnitService $courseUnitService
    ) 
    {
        // Temporarily commenting out middleware to test
        // $this->middleware('can:questions/index')->only('index');
        // $this->middleware('can:questions/create')->only(['create', 'store', 'cloneItem']);
        // $this->middleware('can:questions/edit')->only(['edit', 'update', 'sortView', 'sortUpdate']);
        // $this->middleware('can:questions/delete')->only('destroy', 'bulkDelete');
    }

    /**
     * Display a listing of questions
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $questions = $this->service->getFilteredData($filters);
        $searchConfig = $this->service->getSearchConfig();
        
        // Get filter options
        $questionBanks = $this->questionBankService->getAll();
        $courses = $this->courseService->getAll();
        $questionTypes = $this->service->getQuestionTypes();
        $difficultyLevels = $this->service->getDifficultyLevels();

        return view('admin.questions.index', [
            'page_title' => 'Questions Management',
            'list_items' => $questions,
            'search_config' => $searchConfig,
            'question_banks' => $questionBanks,
            'courses' => $courses,
            'question_types' => $questionTypes,
            'difficulty_levels' => $difficultyLevels,
            'filters' => $filters
        ]);
    }

    /**
     * Show the form for creating a new question
     */
    public function create()
    {
        $questionBanks = $this->questionBankService->getAll();
        $courses = $this->courseService->getAll();
        $questionTypes = $this->service->getQuestionTypes();
        $difficultyLevels = $this->service->getDifficultyLevels();

        return view('admin.questions.create-page', [
            'page_title' => 'Create Questions',
            'question_banks' => $questionBanks,
            'courses' => $courses,
            'question_types' => $questionTypes,
            'difficulty_levels' => $difficultyLevels
        ]);
        // return view('admin.questions.create', [
        //     'page_title' => 'Create Question',
        //     'question_banks' => $questionBanks,
        //     'courses' => $courses,
        //     'question_types' => $questionTypes,
        //     'difficulty_levels' => $difficultyLevels
        // ]);
    }

    /**
     * Store a newly created question
     */
    public function store(StoreQuestionRequest $request)
    {
        // dd($request->all());
        // dd($request->validated());
        try {

            $this->service->store($request->validated());

            return $this->redirectWithSuccess(
                'admin.questions.create',
                'Question created successfully',
                [
                    'course_id' => $request->validated('course_id'),
                    'bank_id' => $request->validated('bank_id'),
                    'question_type' => $request->validated('question_type')
                ]
            );
            
        } catch (\Exception $e) {

            return $this->redirectBackWithError($e->getMessage());
            
        }
        // try {
        //     $this->service->store($request->validated());
            // return $this->successResponse('Question created successfully');
        // } catch (\Exception $e) {
        //     return $this->errorResponse('Failed to create question: ' . $e->getMessage());
        // }
    }

    /**
     * Display the specified question
     */
    public function show(string $id)
    {
        $question = $this->service->findWithDetails($id);
        
        if (!$question) {
            return $this->redirectWithError('admin.questions.index', 'Question not found');
        }

        return view('admin.questions.show', [
            'page_title' => 'Question Details',
            'question' => $question,
        ]);
    }

    /**
     * Show the form for editing the specified question
     */
    public function edit(string $id)
    {
        $edit_data = $this->service->findWithDetails($id);
        
        if (!$edit_data) {
            return $this->redirectWithError('admin.questions.index', 'Question not found');
        }
        // dd($edit_data->questionOptions);

        $questionBanks = $this->questionBankService->getAll();
        $courses = $this->courseService->getAll();
        $questionTypes = $this->service->getQuestionTypes();
        $difficultyLevels = $this->service->getDifficultyLevels();
        // dd($edit_data->questionOptions->first()->option_image_url);
        return view('admin.questions.edit', [
            'page_title' => 'Edit Question',
            'edit_data' => $edit_data,
            'question_banks' => $questionBanks,
            'courses' => $courses,
            'question_types' => $questionTypes,
            'difficulty_levels' => $difficultyLevels,
        ]);
    }

    /**
     * Update the specified question
     */
    public function update(UpdateQuestionRequest $request, string $id)
    {
        // dd($request->validated());
        try {
            // dd($request->validated());
            $this->service->update($id, $request->validated());
            return $this->successResponse('Question updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update question: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question
     */
    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return $this->successResponse('Question deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete question: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete questions
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:questions,id'
        ]);

        try {
            $deletedCount = $this->service->bulkDelete($request->ids);
            
            if ($deletedCount === 0) {
                return $this->errorResponse('No questions were deleted');
            }

            return $this->successResponse("Successfully deleted {$deletedCount} question(s)");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete questions: ' . $e->getMessage());
        }
    }

    /**
     * Update sort order
     */
    public function sortUpdate(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:questions,id'
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
     * Toggle question status
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $question = $this->service->find($id);
            
            if (!$question) {
                return $this->errorResponse('Question not found');
            }

            $newStatus = $question->status === 'active' ? 'inactive' : 'active';
            $this->service->update($id, ['status' => $newStatus]);

            return $this->successResponse("Question status updated to {$newStatus}");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to toggle question status: ' . $e->getMessage());
        }
    }

    /**
     * Get units by course (AJAX)
     */
    public function getUnits(Request $request)
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return response()->json(['units' => []]);
        }

        $units = $this->courseUnitService->getByCourse($courseId);
        
        return response()->json([
            'units' => $units->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'title' => $unit->title
                ];
            })
        ]);
    }

    /**
     * Clone question
     */
    public function cloneItem(Request $request, string $id)
    {
        try {
            $question = $this->service->find($id);
            
            if (!$question) {
                return $this->errorResponse('Question not found');
            }

            $clonedData = $question->toArray();
            unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);
            $clonedData['title'] = $clonedData['question_text'] . ' (Copy)';

            $clonedQuestion = $this->service->store($clonedData);

            return $this->successResponse('Question cloned successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to clone question: ' . $e->getMessage());
        }
    }

    /**
     * Sort view
     */
    public function sortView()
    {
        $questions = $this->service->getAll();
        
        return view('admin.questions.sort', [
            'page_title' => 'Sort Questions',
            'questions' => $questions,
        ]);
    }

    public function loadFields(Request $request)
    {
        $question_type = $request->get('question_type');
        $input_data = $request->get('input_data') ?? [];
        if ($question_type === 'mcq_single' || $question_type === 'mcq_multiple' || $question_type === 'true_false') {
            return view('admin.questions.fields.options-repeater-field',[
                'name' => 'options',
                'id' => 'options',
                'itemLabel' => 'Option',
                'itemIcon' => 'fas fa-list',
                'options_data' => $input_data['options'] ?? [],
                'questionType' => $question_type
            ]);
        } else if($question_type === 'fill_blank') {
             
            return view('admin.questions.fields.fill-blanks-field', [
                'blanks_data' => $input_data['blanks'] ?? [],
            ]);
        } else if($question_type === 'short_answer' || $question_type === 'descriptive') {

            $label = $question_type == 'short_answer' ? 'Short answer text' : 'Descriptive answer text';
            $name = $question_type == 'short_answer' ? 'short_answer_text' : 'descriptive_answer_text';
            $id = $question_type == 'short_answer' ? 'short-answer' : 'descriptive-answer';
            
            return view('admin.questions.fields.short-descriptive-field',[
                'label' => $label,
                'name'=> $name,
                'id'=> $id,
                'answer_text' => $input_data['answer_text'] ?? null
            ]);

        }
        return $this->errorResponse('Invalid question type: ' . $question_type);
    }
}

