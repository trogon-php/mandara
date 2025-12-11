<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Exams\StoreExamRequest;
use App\Http\Requests\Exams\UpdateExamRequest;
use App\Services\Exams\ExamService;
use App\Services\CourseMaterials\CourseMaterialService;
use App\Services\Questions\QuestionService;
use App\Services\QuestionBanks\QuestionBankService;
use App\Services\Courses\CourseService;
use App\Services\CourseUnits\CourseUnitService;
use App\Services\Users\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ExamController extends AdminBaseController
{
    public function __construct(
        private ExamService $service,
        private CourseMaterialService $courseMaterialService,
        private QuestionService $questionService,
        private QuestionBankService $questionBankService,
        private CourseService $courseService,
        private CourseUnitService $courseUnitService
    ) 
    {
        // Temporarily commenting out middleware to test
        // $this->middleware('can:exams/index')->only('index');
        // $this->middleware('can:exams/create')->only(['create', 'store', 'cloneItem']);
        // $this->middleware('can:exams/edit')->only(['edit', 'update', 'sortView', 'sortUpdate']);
        // $this->middleware('can:exams/delete')->only('destroy', 'bulkDelete');
    }

    /**
     * Display a listing of exams
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $exams = $this->service->getFilteredData($filters);
        $searchConfig = $this->service->getSearchConfig();
        $filterConfig = $this->service->getFilterConfig();
        
        // Get filter options
        $courses = $this->courseService->getAll();
        $statusOptions = $this->service->getStatusOptions();

        return view('admin.exams.index', [
            'page_title' => 'Exams Management',
            'list_items' => $exams,
            'search_config' => $searchConfig,
            'filter_config' => $filterConfig,
            'courses' => $courses,
            'status_options' => $statusOptions,
            'filters' => $filters
        ]);
    }

    /**
     * Show the form for creating a new exam
     */
    public function create()
    {
        $questionBanks = $this->questionBankService->getAll();
        $courses = $this->courseService->getAll();
        $questionTypes = $this->service->getQuestionTypes();
        $difficultyLevels = $this->service->getDifficultyLevels();

        return view('admin.exams.create', [
            'page_title' => 'Create Exam',
            'question_banks' => $questionBanks,
            'courses' => $courses,
            'question_types' => $questionTypes,
            'difficulty_levels' => $difficultyLevels
        ]);
    }

    /**
     * Store a newly created exam
     */
    public function store(StoreExamRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return $this->successResponse('Exam created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create exam: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam
     */
    public function show(string $id)
    {
        $exam = $this->service->findWithDetails($id);
        
        if (!$exam) {
            return $this->redirectWithError('admin.exams.index', 'Exam not found');
        }

        $stats = $this->service->getExamStats($id);

        return view('admin.exams.show', [
            'page_title' => 'Exam Details',
            'exam' => $exam,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for editing the specified exam
     */
    public function edit(string $id)
    {
        $edit_data = $this->service->findWithDetails($id);
        
        if (!$edit_data) {
            return $this->redirectWithError('admin.exams.index', 'Exam not found');
        }

        $questionBanks = $this->questionBankService->getAll();
        $courses = $this->courseService->getAll();
        $questionTypes = $this->service->getQuestionTypes();
        $difficultyLevels = $this->service->getDifficultyLevels();

        return view('admin.exams.edit', [
            'page_title' => 'Edit Exam',
            'edit_data' => $edit_data,
            'question_banks' => $questionBanks,
            'courses' => $courses,
            'question_types' => $questionTypes,
            'difficulty_levels' => $difficultyLevels
        ]);
    }

    /**
     * Update the specified exam
     */
    public function update(UpdateExamRequest $request, string $id)
    {
        try {
            $this->service->update($id, $request->validated());
            return $this->successResponse('Exam updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update exam: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam
     */
    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return $this->successResponse('Exam deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete exams
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:exams,id'
        ]);

        try {
            $deletedCount = $this->service->bulkDelete($request->ids);
            
            if ($deletedCount === 0) {
                return $this->errorResponse('No exams were deleted');
            }

            return $this->successResponse("Successfully deleted {$deletedCount} exam(s)");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete exams: ' . $e->getMessage());
        }
    }
    // Bulk remove questions
    public function bulkRemoveExamQuestions(Request $request, int $id)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:exam_questions,id'
        ]);

        if (!$this->service->bulkRemoveExamQuestions($id,$request->ids)) {
            return $this->errorResponse('Failed to delete items');
        }
        return $this->successResponse('Selected items removed successfully');
    }

    /**
     * Toggle exam status
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $exam = $this->service->find($id);
            
            if (!$exam) {
                return $this->errorResponse('Exam not found');
            }

            $newStatus = $exam->status === 'active' ? 'inactive' : 'active';
            $this->service->update($id, ['status' => $newStatus]);

            return $this->successResponse("Exam status updated to {$newStatus}");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to toggle exam status: ' . $e->getMessage());
        }
    }

    /**
     * Get questions for exam (AJAX)
     */
    public function getQuestions(Request $request)
    {
        $filters = $request->all();
        $questions = $this->service->getAvailableQuestions($filters);
        
        return response()->json([
            'questions' => $questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'difficulty' => $question->difficulty,
                    'bank' => $question->bank->title ?? '',
                    'course' => $question->course->title ?? '',
                    'unit' => $question->unit->title ?? '',
                    'options' => $question->questionOptions->map(function ($option) {
                        return [
                            'option_text' => $option->option_text,
                            'is_correct' => $option->is_correct
                        ];
                    })
                ];
            })
        ]);
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
     * Get materials by course (AJAX)
     */
    public function getMaterials(Request $request)
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return response()->json(['materials' => []]);
        }

        $materials = $this->courseMaterialService->getByCourse($courseId);
        
        return response()->json([
            'materials' => $materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'title' => $material->title,
                    'type' => $material->type
                ];
            })
        ]);
    }

    /**
     * Clone exam
     */
    public function cloneItem(Request $request, string $id)
    {
        try {
            $exam = $this->service->find($id);
            
            if (!$exam) {
                return $this->errorResponse('Exam not found');
            }

            $clonedData = $exam->toArray();
            unset($clonedData['id'], $clonedData['created_at'], $clonedData['updated_at']);
            $clonedData['title'] = $clonedData['title'] . ' (Copy)';

            $clonedExam = $this->service->store($clonedData);

            return $this->successResponse('Exam cloned successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to clone exam: ' . $e->getMessage());
        }
    }

    /**
     * Manage exam questions
     */
    public function questions(Request $request, string $id)
    {
        $exam = $this->service->findWithDetails($id);
        
        if (!$exam) {
            return $this->redirectWithError('admin.exams.index', 'Exam not found');
        }

        $filters = $request->only(['bank_id', 'course_id', 'question_type', 'difficulty']);

        if ($request->ajax()) {

            $columns = ['question.question_text'];
            $filters = $request->input('filters') ?? [];

            return $this->service->questionsDataTable(
                $request,
                $columns,
                $filters,
                function ($examQuestion) use($exam) {
                            return [
                                '<input type="checkbox" class="form-check-input row-checkbox" value="'.$examQuestion->id.'">',
                                '<strong class="text-primary">#'.$examQuestion->question->id.'</strong>',
                                '<div class="fw-bold">'.Str::limit($examQuestion->question->question_text, 150).'</div>',
                                view('admin.exams.datatable_columns.questions.options-answer',compact('examQuestion'))->render(),
                                view('admin.exams.datatable_columns.questions.bank-course',compact('examQuestion'))->render(),
                                view('admin.exams.datatable_columns.questions.remove-action',compact('examQuestion','exam'))->render(),
                            ];
                        },
                        ['exam_id' => $exam->id]
                );
        }

        return view('admin.exams.questions', [
            'page_title' => 'Manage Exam Questions',
            'exam' => $exam,
            'filterConfig' => [],
            'searchConfig' => $this->service->getQuestionsSearchConfig(),
        ]);
    }

    public function addQuestionIndex(Request $request, $examId)
    {
        $exam = $this->service->find($examId);

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
                function ($question) use($exam) {
                            $isAlreadyAdded = $exam->examQuestions->pluck('question_id')->contains($question->id);
                            $limitExceeded = $exam->examQuestions->count() == $exam->questions_limit;
                            return [
                                view('admin.exams.datatable_columns.questions_add.options-answer',
                                compact('question'))
                                        ->render(),
                                view('admin.exams.datatable_columns.questions_add.add-action',
                                compact('isAlreadyAdded','question','exam','limitExceeded'))
                                        ->render(),
                            ];
                        }
                );
        }
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        return view('admin.exams.index-add-questions', [
            'page_title' => 'Add Questions to '. $exam->title,
            'exam' => $exam,
            'filters' => $filters,
            // 'search_params' => $searchParams,
            'filterConfig' => $this->service->getAddQuestionsFilterConfig([
                'questionBanks' => $this->questionBankService->getFlatQuestionBankOptions(),
                'courses' => $this->courseService->getIdTitle(),
                'questionTypes' => $this->service->getQuestionTypes(),
                'difficultyLevels' => $this->service->getDifficultyLevels(),
            ]),
            'searchConfig' => $this->service->getQuestionsSearchConfig(),
        ]);
    }

    /**
     * Add question to exam
     */
    public function addQuestion(Request $request, string $id)
    {
        try {
            $exam = $this->service->find($id);
            
            if (!$exam) {
                return response()->json(['success' => false, 'message' => 'Exam not found']);
            }

            $questionId = $request->input('question_id');
            
            if (!$questionId) {
                return response()->json(['success' => false, 'message' => 'Question ID is required']);
            }

            // Check if question already exists in exam
            $existingQuestion = $exam->examQuestions()->where('question_id', $questionId)->first();
            
            if ($existingQuestion) {
                return response()->json(['success' => false, 'message' => 'Question already exists in this exam']);
            }

            // Check total questions count vs limit
            $currentCount = $exam->total_questions;
            $limit = $exam->questions_limit ?? null;
            
            if ($limit && $currentCount >= $limit) {
                return response()->json([
                    'success' => false,
                    'message' => "Question limit reached ({$limit} questions allowed)"
                ]);
            }

            $question = $this->questionService->find($questionId);
            // Add question to exam
            $exam->examQuestions()->create([
                'question_id' => $questionId,
                'mark_correct' => $exam->mark_correct ?? $question->mark_correct,
                'mark_wrong' => $exam->mark_wrong ?? $question->mark_wrong
            ]);

            return response()->json(['success' => true, 'message' => 'Question added successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add question: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove question from exam
     */
    public function removeQuestion(Request $request, string $id)
    {
        try {
            $exam = $this->service->find($id);
            
            if (!$exam) {
                return response()->json(['success' => false, 'message' => 'Exam not found']);
            }

            $examQuestionId = $request->input('exam_question_id');
            
            if (!$examQuestionId) {
                return response()->json(['success' => false, 'message' => 'Exam question ID is required']);
            }

            // Remove question from exam
            $exam->examQuestions()->where('id', $examQuestionId)->delete();

            return response()->json(['success' => true, 'message' => 'Question removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to remove question: ' . $e->getMessage()]);
        }
    }

    /**
     * Get exam attempts
     */
    public function reports(Request $request, string $id)
    {
        $filters = $request->all();
        $reports = $this->service->getExamReports($id, $filters);
        
        return view('admin.exams.reports', [
            'page_title' => 'Exam Reports',
            'exam_id' => $id,
            'reports' => $reports,
            'filters' => $filters
        ]);
    }

    /**
     * Get exam statistics
     */
    public function stats(string $id)
    {
        $exam = $this->service->findWithDetails($id);
        
        if (!$exam) {
            return $this->redirectWithError('admin.exams.index', 'Exam not found');
        }

        $stats = $this->service->getExamStats($id);
        // dd($stats);
        
        return view('admin.exams.stats', [
            'page_title' => 'Exam Statistics',
            'exam' => $exam,
            'stats' => $stats
        ]);
    }

    /**
     * ------------------Show exam attend page (instructions/pre-attempt) ------------------------------------------
     */
    public function attend(string $id)
    {
        $exam = $this->service->findWithDetails($id);
        
        if (!$exam) {
            return $this->redirectWithError('admin.exams.index', 'Exam not found');
        }

        // Check for existing in-progress attempt
        $user = auth()->user();
        $inProgressAttempt = \App\Models\ExamAttempt::where('exam_id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        // Get user's previous attempts
        $userAttempts = \App\Models\ExamAttempt::where('exam_id', $id)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $remainingAttempts = max(0, $exam->attempts - $userAttempts->count());
        
        // Calculate exam timing information
        $now = now();
        $examStatus = 'available';
        $timeStatus = null;
        $timeMessage = null;
        $canStart = $remainingAttempts > 0 && $exam->status === 'active';
        
        // Check exam time window
        if ($exam->start_time && $exam->end_time) {
            if ($now < $exam->start_time) {
                $examStatus = 'upcoming';
                $timeStatus = 'before_start';
                $timeMessage = 'Exam has not started yet';
                $canStart = false;
            } elseif ($now > $exam->end_time) {
                $examStatus = 'ended';
                $timeStatus = 'after_end';
                $timeMessage = 'Exam has ended';
                $canStart = false;
            } else {
                $examStatus = 'active';
                $timeStatus = 'in_window';
                $timeMessage = 'Exam is currently available';
            }
        } elseif ($exam->start_time && $now < $exam->start_time) {
            $examStatus = 'upcoming';
            $timeStatus = 'before_start';
            $timeMessage = 'Exam has not started yet';
            $canStart = false;
        } elseif ($exam->end_time && $now > $exam->end_time) {
            $examStatus = 'ended';
            $timeStatus = 'after_end';
            $timeMessage = 'Exam has ended';
            $canStart = false;
        }
        
        // Calculate time remaining until start/end
        $secondsUntilStart = null;
        $secondsUntilEnd = null;
        if ($exam->start_time && $now < $exam->start_time) {
            $secondsUntilStart = max(0, $now->diffInSeconds($exam->start_time, false));
        }
        if ($exam->end_time && $now < $exam->end_time) {
            $secondsUntilEnd = max(0, $now->diffInSeconds($exam->end_time, false));
        }

        return view('admin.exams.exam_attend.attend', [
            'page_title' => 'Attend Exam: ' . $exam->title,
            'exam' => $exam,
            'in_progress_attempt' => $inProgressAttempt,
            'user_attempts' => $userAttempts,
            'remaining_attempts' => $remainingAttempts,
            'can_start' => $canStart,
            'exam_status' => $examStatus,
            'time_status' => $timeStatus,
            'time_message' => $timeMessage,
            'seconds_until_start' => $secondsUntilStart,
            'seconds_until_end' => $secondsUntilEnd,
            'now' => $now
        ]);
    }

    /**
     * Start exam attempt
     */
    public function startAttempt(Request $request, string $id)
    {
        try {
            $user = auth()->user();
            $examAttemptService = app(\App\Services\Exams\ExamAttemptService::class);
            
            // Check for existing in-progress attempt
            $existingAttempt = \App\Models\ExamAttempt::where('exam_id', $id)
                ->where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->first();
                
            if ($existingAttempt) {
                return response()->json([
                    'success' => true,
                    'message' => 'Resuming existing attempt',
                    'attempt_id' => $existingAttempt->id,
                    'redirect' => route('admin.exams.attempt', ['exam' => $id, 'attemptId' => $existingAttempt->id])
                ]);
            }

            $result = $examAttemptService->startExamAttempt($id, $user->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Exam started successfully',
                'data' => $result,
                'redirect' => route('admin.exams.attempt', ['exam' => $id, 'attemptId' => $result['attempt_id']])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show exam attempt interface
     */
    public function attempt(string $id, string $attemptId)
    {
        $exam = $this->service->findWithDetails($id);
        $user = auth()->user();
        
        if (!$exam) {
            return $this->redirectWithError('admin.exams.index', 'Exam not found');
        }

        $attempt = \App\Models\ExamAttempt::where('id', $attemptId)
            ->where('exam_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$attempt) {
            return $this->redirectWithError('admin.exams.attend', ['exam' => $id], 'Attempt not found');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('admin.exams.review', ['exam' => $id, 'attemptId' => $attemptId])
                ->with('info', 'This attempt has already been submitted. Showing review.');
        }

        // Get question order
        $questionOrder = $attempt->question_order ?? [];
        $firstQuestionId = $questionOrder[0] ?? null;

        if (!$firstQuestionId) {
            return $this->redirectWithError('admin.exams.attend', ['exam' => $id], 'No questions found in this attempt');
        }

        // Calculate remaining time based on exam window and time limit
        $remainingSeconds = null;
        $expiresAt = null;
        
        if ($exam->time_limit) {
            $startedAt = $attempt->started_at;
            $now = now();
            
            // Calculate time based on exam window (start_time to end_time)
            $examWindowEnd = null;
            if ($exam->end_time) {
                $examWindowEnd = $exam->end_time;
            }
            
            // Calculate time based on duration from when user started
            $durationExpiresAt = $startedAt->copy()->addMinutes($exam->time_limit);
            
            // The actual expiration is the minimum of:
            // 1. Exam window end time (if exists)
            // 2. Duration from when user started
            if ($examWindowEnd) {
                $expiresAt = $durationExpiresAt->lt($examWindowEnd) ? $durationExpiresAt : $examWindowEnd;
            } else {
                $expiresAt = $durationExpiresAt;
            }
            
            // Calculate remaining seconds
            $remainingSeconds = max(0, $now->diffInSeconds($expiresAt, false));
        }

        return view('admin.exams.exam_attend.attempt', [
            'page_title' => 'Taking Exam: ' . $exam->title,
            'exam' => $exam,
            'attempt' => $attempt,
            'question_order' => $questionOrder,
            'total_questions' => count($questionOrder),
            'first_question_id' => $firstQuestionId,
            'remaining_seconds' => $remainingSeconds,
            'expires_at' => $expiresAt ? $expiresAt->toIso8601String() : null
        ]);
    }

    /**
     * Resume in-progress attempt
     */
    public function resumeAttempt(string $id, string $attemptId)
    {
        $user = auth()->user();
        $attempt = \App\Models\ExamAttempt::where('id', $attemptId)
            ->where('exam_id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return response()->json([
                'success' => false,
                'message' => 'No in-progress attempt found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'redirect' => route('admin.exams.attempt', ['exam' => $id, 'attemptId' => $attemptId])
        ]);
    }

    /**
     * Get question for attempt (AJAX)
     */
    public function getQuestion(string $id, string $attemptId, string $questionId)
    {
        try {
            $user = auth()->user();
            $examAttemptService = app(\App\Services\Exams\ExamAttemptService::class);
            
            // Verify attempt ownership
            $attempt = \App\Models\ExamAttempt::where('id', $attemptId)
                ->where('exam_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$attempt || $attempt->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid attempt or attempt not in progress'
                ], 400);
            }

            $questionData = $examAttemptService->getQuestionForAttempt($attemptId, $questionId);
            
            return response()->json([
                'success' => true,
                'data' => $questionData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Save answer (AJAX)
     */
    public function saveAnswer(Request $request, string $id, string $attemptId)
    {
        try {
            $user = auth()->user();
            $examAttemptService = app(\App\Services\Exams\ExamAttemptService::class);
            
            // Verify attempt ownership
            $attempt = \App\Models\ExamAttempt::where('id', $attemptId)
                ->where('exam_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$attempt || $attempt->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid attempt or attempt not in progress'
                ], 400);
            }

            $request->validate([
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|integer',
                'answers.*.selected_option_ids' => 'nullable|array',
                'answers.*.answer_text' => 'nullable|string',
                'answers.*.time_spent' => 'nullable|integer|min:0',
                'answers.*.is_flagged' => 'nullable|in:0,1',
                'answers.*.action' => 'nullable|string|in:clear,skip'  // Add this line
            ]);

            $result = $examAttemptService->saveProgress($attemptId, $request->input('answers'));
            
            return response()->json([
                'success' => true,
                'message' => 'Answer saved successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Submit exam attempt
     */
    public function submitAttempt(Request $request, string $id, string $attemptId)
    {
        try {
            $user = auth()->user();
            $examAttemptService = app(\App\Services\Exams\ExamAttemptService::class);
            
            // Verify attempt ownership
            $attempt = \App\Models\ExamAttempt::where('id', $attemptId)
                ->where('exam_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$attempt || $attempt->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid attempt or attempt already submitted'
                ], 400);
            }

            $result = $examAttemptService->submitAttempt($attemptId);
            
            return response()->json([
                'success' => true,
                'message' => 'Exam submitted successfully',
                'data' => $result,
                'redirect' => route('admin.exams.review', ['exam' => $id, 'attemptId' => $attemptId])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Check time remaining (AJAX)
     */
    // public function checkTime(string $id, string $attemptId)
    // {
    //     try {
    //         $user = auth()->user();
    //         $examAttemptService = app(\App\Services\Exams\ExamAttemptService::class);
            
    //         $attempt = \App\Models\ExamAttempt::where('id', $attemptId)
    //             ->where('exam_id', $id)
    //             ->where('user_id', $user->id)
    //             ->first();

    //         if (!$attempt) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Attempt not found'
    //             ], 404);
    //         }

    //         $exam = $attempt->exam;
    //         $startedAt = $attempt->started_at;
    //         $timeLimit = $exam->time_limit;
            
    //         if (!$timeLimit) {
    //             return response()->json([
    //                 'success' => true,
    //                 'unlimited' => true
    //             ]);
    //         }

    //         $expiresAt = $startedAt->copy()->addMinutes($timeLimit);
    //         $now = now();
    //         $remainingSeconds = max(0, $now->diffInSeconds($expiresAt, false));
    //         $isExpired = $now >= $expiresAt;

    //         return response()->json([
    //             'success' => true,
    //             'expired' => $isExpired,
    //             'remaining_seconds' => $remainingSeconds,
    //             'expires_at' => $expiresAt->toIso8601String(),
    //             'formatted' => $this->formatTime($remainingSeconds)
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 400);
    //     }
    // }

    /**
     * Show exam review/results
     */
    public function review(Request $request, string $id, string $attemptId)
    {
        $userId = $request->userId ?? null;
        $exam = $this->service->findWithDetails($id);
        // $user = auth()->user();
        
        if (!$exam) {
            return $this->redirectWithError('admin.exams.index', 'Exam not found');
        }
        if($userId){
            $user = app(UserService::class)->find($userId);
            if(!$user){
                return $this->redirectBackWithError('User not found');
            }
        }else {
            return $this->redirectBackWithError('User ID is required');
        }
    
        $attempt = \App\Models\ExamAttempt::where('id', $attemptId)
            ->where('exam_id', $id)
            ->where('user_id', $userId)
            ->with(['examAnswers.question.questionOptions', 'examAnswers.question.questionFillBlanks'])
            ->first();
    
        if (!$attempt) {
            // dd('Attempt not found');
            // return $this->redirectWithError('admin.exams.attend', 'Attempt not found');
            return $this->redirectBackWithError('Attempt not found');
        }
    
        // Get all questions with answers
        $questionOrder = $attempt->question_order ?? [];
        $questionsWithAnswers = [];
        
        foreach ($questionOrder as $questionId) {
            $question = \App\Models\Question::with(['questionOptions', 'questionMatchPairs', 'questionFillBlanks'])->find($questionId);
            $answer = $attempt->examAnswers->where('question_id', $questionId)->first();
            
            // Determine status based on question type
            $status = 'skipped';
            $isCorrect = false;
            $isPendingReview = false;
            
            if ($answer) {
                $status = $answer->status ?? 'ungraded';
                
                // Check if question type requires manual review
                if (in_array($question->question_type, ['descriptive', 'short_answer'])) {
                    // Only descriptive and short_answer should show pending review
                    if ($status === 'ungraded' || $status === 'pending_review') {
                        $isPendingReview = true;
                        $isCorrect = false;
                    } else {
                        // If already evaluated, show the result
                        $isPendingReview = false;
                        $isCorrect = $status === 'correct';
                    }
                } else {
                    // Option-based questions (MCQ, fill blanks, true/false)
                    // If status is ungraded, we should evaluate it or show as wrong/skipped
                    if ($status === 'ungraded') {
                        // For option-based questions, if ungraded, it means not evaluated yet
                        // You can either evaluate here or show as not answered
                        // For now, let's check if there's an answer and evaluate on-the-fly
                        $isPendingReview = false;
                        $isCorrect = false; // Will be evaluated below or already should be evaluated
                    } else {
                        $isPendingReview = false;
                        $isCorrect = $status === 'correct';
                    }
                }
            }
            
            $questionsWithAnswers[] = [
                'question' => $question,
                'answer' => $answer,
                'is_correct' => $isCorrect,
                'status' => $status,
                'is_pending_review' => $isPendingReview
            ];
        }
        // dd($attempt,$questionsWithAnswers);
        return view('admin.exams.exam_attend.review', [
            'page_title' => 'Exam Review: ' . $exam->title,
            'exam' => $exam,
            'attempt' => $attempt,
            'questions_with_answers' => $questionsWithAnswers
        ]);
    }

    /**
     * Helper: Format time in HH:MM:SS
     */
    private function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
