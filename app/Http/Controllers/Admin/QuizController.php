<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Quizzes\{

    StoreQuizRequest as StoreRequest,
    UpdateQuizRequest as UpdateRequest,

};
use App\Services\{

    Questions\QuestionService,
    CourseMaterials\CourseMaterialService,
    Courses\CourseService,
    CourseUnits\CourseUnitService,
    QuestionBanks\QuestionBankService,
    Quizzes\QuizService,

};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizController extends AdminBaseController
{
    public function __construct(
        private QuizService $service,
        private CourseMaterialService $courseMaterialService,
        private QuestionService $questionService,
        private QuestionBankService $questionBankService,
        private CourseService $courseService,
        private CourseUnitService $courseUnitService
        ) {}

    public function index(Request $request)
    {
        $filters = $request->all();
        $quizzes = $this->service->getFilteredData($filters);
        
        // Get filter options
        $courses = $this->courseService->getAll();
        $statusOptions = $this->service->getStatusOptions();

        return view('admin.quizzes.index', [
            'page_title' => 'Quizzes List',
            'list_items' => $quizzes,
            'filters' => $filters,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
            'courses' => $courses,
            'status_options' => $statusOptions,
        ]);
    }

    public function create() { 
        $questionBanks = $this->questionBankService->getAll();
        $courses = $this->courseService->getAll();
        $questionTypes = $this->service->getQuestionTypes();
        $difficultyLevels = $this->service->getDifficultyLevels();

        return view('admin.quizzes.create',[
            'page_title' => 'Create Quiz',
            'question_banks' => $questionBanks,
            'courses' => $courses,
            'question_types' => $questionTypes,
            'difficulty_levels' => $difficultyLevels
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Item created successfully');
    }

    public function edit(string $id)
    {
        $edit_data = $this->service->find($id);
        if (!$edit_data) {
            return $this->redirectWithError('admin.exams.index', 'Exam not found');
        }

        $questionBanks = $this->questionBankService->getAll();
        $courses = $this->courseService->getAll();
        $questionTypes = $this->service->getQuestionTypes();
        $difficultyLevels = $this->service->getDifficultyLevels();

        return view('admin.quizzes.edit', [
            'page_title' => 'Edit Exam',
            'edit_data' => $edit_data,
            'question_banks' => $questionBanks,
            'courses' => $courses,
            'question_types' => $questionTypes,
            'difficulty_levels' => $difficultyLevels
        ]);
    }

    public function update(UpdateRequest $request, string $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Item updated successfully');
    }

    public function destroy(string $id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
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
        return view('admin.quizzes.sort', ['list_items'=>$list_items]);
    }

    public function cloneItem($id)
    {
        $item = $this->service->find($id);
        $cloned = $this->service->clone($item);
        if (!$cloned) return $this->errorResponse('Failed to clone item.');
        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal',
            'url' => route('admin.quizzes.edit', $cloned->id)
        ]);
    }

    /**
     * Manage Quiz Questions
     */
    public function questions(Request $request, string $id)
    {
        $quiz = $this->service->find($id,[
            'material.course',
            'quizQuestions.question.questionOptions',
            'quizQuestions.question.questionMatchPairs'
        ]);
        // dd($quiz);
        if (!$quiz) {
            return $this->redirectWithError('admin.quizzes.index', 'Quiz not found');
        }

        $filters = $request->only(['bank_id', 'course_id', 'question_type', 'difficulty']);

        if ($request->ajax()) {

            $columns = ['question.question_text'];
            $filters = $request->input('filters') ?? [];

            return $this->service->questionsDataTable(
                $request,
                $columns,
                $filters,
                function ($quizQuestion) use($quiz) {
                            return [
                                '<input type="checkbox" class="form-check-input row-checkbox" value="'.$quizQuestion->id.'">',
                                '<strong class="text-primary">#'.$quizQuestion->question->id.'</strong>',
                                '<div class="fw-bold">'.Str::limit($quizQuestion->question->question_text, 150).'</div>',
                                view('admin.quizzes.datatable_columns.questions.options-answer',
                                compact('quizQuestion'))
                                    ->render(),
                                view('admin.quizzes.datatable_columns.questions.bank-course',
                                compact('quizQuestion'))
                                    ->render(),
                                view('admin.quizzes.datatable_columns.questions.remove-action',
                                compact('quizQuestion','quiz'))
                                    ->render(),
                            ];
                        },
                        ['quiz_id' => $quiz->id]
                );
        }

        return view('admin.quizzes.questions', [
            'page_title' => 'Manage Quiz Questions',
            'quiz' => $quiz,
            'filterConfig' => [],
            'searchConfig' => [],
        ]);
    }
    // Add questions index page
    public function addQuestionIndex(Request $request, $quizId)
    {
        $quiz = $this->service->find($quizId);

        $filters = $request->only(['bank_id', 'course_id', 'question_type', 'difficulty']);
        // dd($filters);

        if ($request->ajax()) {
            // dd('is ajax request', $filters, $request->input());
            $columns = ['question_text'];
            $filters = $request->input('filters') ?? [];

            // dd($filters);
            return $this->service->addQuestionsDataTable(
                $request,
                $columns,
                $filters,
                function ($question) use($quiz) {
                            $isAlreadyAdded = $quiz->quizQuestions->pluck('question_id')->contains($question->id);
                            $limitExceeded = $quiz->total_questions == $quiz->questions_limit;
                            return [
                                view('admin.quizzes.datatable_columns.questions_add.options-answer',
                                compact('question'))
                                        ->render(),
                                view('admin.quizzes.datatable_columns.questions_add.add-action',
                                compact('isAlreadyAdded','question','quiz','limitExceeded'))
                                        ->render(),
                            ];
                        }
                );
        }
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        return view('admin.quizzes.index-add-questions', [
            'page_title' => 'Add Questions to '. $quiz->title,
            'quiz' => $quiz,
            'filters' => $filters,
            // 'search_params' => $searchParams,
            'filterConfig' => $this->service->getAddQuestionsFilterConfig([
                'questionBanks' => $this->questionBankService->getFlatQuestionBankOptions(),
                'courses' => $this->courseService->getIdTitle(),
                'questionTypes' => $this->service->getQuestionTypes(),
                'difficultyLevels' => $this->service->getDifficultyLevels(),
            ]),
            'searchConfig' => ['search_fields' => ['title']],
        ]);
    }
    /**
     * Add question to exam
     */
    public function addQuestion(Request $request, string $id)
    {
        try {
            $quiz = $this->service->find($id);
            
            if (!$quiz) {
                return response()->json(['success' => false, 'message' => 'Quiz not found']);
            }

            $questionId = $request->input('question_id');
            
            if (!$questionId) {
                return response()->json(['success' => false, 'message' => 'Question ID is required']);
            }

            // Check if question already exists in quiz
            $existingQuestion = $quiz->quizQuestions()->where('question_id', $questionId)->first();
            
            if ($existingQuestion) {
                return response()->json(['success' => false, 'message' => 'Question already exists in this quiz']);
            }

            // Check total questions count vs limit
            $currentCount = $quiz->total_questions;
            $limit = $quiz->questions_limit ?? null;
            
            if ($limit && $currentCount >= $limit) {
                return response()->json([
                    'success' => false,
                    'message' => "Question limit reached ({$limit} questions allowed)"
                ]);
            }

            $question = $this->questionService->find($questionId);
            // Add question to quiz
            $quiz->quizQuestions()->create([
                'question_id' => $questionId,
                'mark_correct' => $quiz->mark_correct ?? $question->mark_correct,
                'mark_wrong' => $quiz->mark_wrong ?? $question->mark_wrong
            ]);

            return response()->json(['success' => true, 'message' => 'Question added successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add question: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove question from quiz
     */
    public function removeQuestion(Request $request, string $id)
    {
        try {
            $quiz = $this->service->find($id);
            
            if (!$quiz) {
                return response()->json(['success' => false, 'message' => 'Quiz not found']);
            }

            $quizQuestionId = $request->input('quiz_question_id');
            
            if (!$quizQuestionId) {
                return response()->json(['success' => false, 'message' => 'Quiz question ID is required']);
            }

            // Remove question from quiz
            $quiz->quizQuestions()->where('id', $quizQuestionId)->delete();

            return response()->json(['success' => true, 'message' => 'Question removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to remove question: ' . $e->getMessage()]);
        }
    }
    // Bulk remove questions
    public function bulkRemoveQuizQuestions(Request $request, int $id)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:quiz_questions,id'
        ]);

        if (!$this->service->bulkRemoveQuizQuestions($id,$request->ids)) {
            return $this->errorResponse('Failed to delete items');
        }
        return $this->successResponse('Selected items removed successfully');
    }
}