<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Qa\AppQaAnswerResource;
use App\Http\Resources\Qa\AppQaCategoryResource;
use App\Http\Resources\Qa\AppQaQuestionResource;
use App\Services\Qa\QaCategoryService;
use App\Services\Qa\QaQuestionService;
use App\Services\Qa\QaAnswerService;
use App\Services\Qa\QaVoteService;
use Illuminate\Http\Request;

class QaController extends BaseApiController
{

    public function __construct(
        protected QaCategoryService $categoryService,
        protected QaQuestionService $questionService,
        protected QaAnswerService $answerService,
        protected QaVoteService $voteService
    ) {}

    /**
     * Get all active categories
     */
    public function categories()
    {
        $categories = $this->categoryService->getActiveCategories();

        $categories = AppQaCategoryResource::collection($categories);
        
        return $this->respondSuccess($categories, 'Categories retrieved successfully');
    }

    /**
     * Get paginated questions
     */
    public function getQuestions(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $categoryId = $request->get('category_id');

        $questions = $this->questionService->getQuestionsPaginated($perPage, $categoryId);
        $questions = AppQaQuestionResource::collection($questions);

        return $this->respondPaginated($questions, 'Questions retrieved successfully');
    }

    /**
     * Store a new question
     */
    public function storeQuestion(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $data = $request->validate([
            'category_id' => 'required|exists:qa_categories,id',
            'question_text' => 'required|string',
        ]);

        $data['user_id'] = $user->id;
        $question = $this->questionService->createUserQuestion($data);
        $question = AppQaQuestionResource::make($question);

        return $this->respondSuccess($question, 'Question created successfully', 201);
    }

    /**
     * Update a question
     */
    public function updateQuestion(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $data = $request->validate([
            'question_text' => 'required|string',
            'category_id' => 'required|exists:qa_categories,id',
        ]);

        $question = $this->questionService->updateUserQuestion($id, $user->id, $data);
        $question = AppQaQuestionResource::make($question);

        if (!$question) {
            return $this->respondError('Question not found or you do not have permission to update it');
        }

        return $this->respondSuccess($question, 'Question updated successfully');
    }

    /**
     * Delete a question
     */
    public function deleteQuestion($id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $result = $this->questionService->deleteUserQuestion($id, $user->id);

        if (!$result) {
            return $this->respondError('Question not found or you do not have permission to delete it', [], 404);
        }

        return $this->respondSuccess([], 'Question deleted successfully');
    }

    /**
     * Get answers for a question
     */
    public function getAnswers(Request $request, $id)
    {
        $perPage = $request->get('per_page', 10);
        $answers = $this->answerService->getQuestionAnswers($id, $perPage);

        $answers = AppQaAnswerResource::collection($answers);

        return $this->respondPaginated($answers, 'Answers retrieved successfully');
    }

    /**
     * Store an answer to a question
     */
    public function storeAnswer(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $data = $request->validate([
            'answer_text' => 'required|string',
        ]);

        $data['question_id'] = $id;
        $data['user_id'] = $user->id;

        $answer = $this->answerService->createUserAnswer($data);
        $answer = AppQaAnswerResource::make($answer);

        return $this->respondSuccess($answer, 'Answer created successfully', 201);
    }

    /**
     * Delete an answer
     */
    public function deleteAnswer($id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $result = $this->answerService->deleteUserAnswer($id, $user->id);

        if (!$result) {
            return $this->respondError('Answer not found or you do not have permission to delete it', [], 404);
        }

        return $this->respondSuccess([], 'Answer deleted successfully');
    }

    /**
     * Store or update vote on a question
     */
    public function storeOrUpdateVote(Request $request, $id)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $data = $request->validate([
            'vote_type' => 'required|string|in:helpful,not_helpful,none',
        ]);

        $result = $this->voteService->storeOrUpdateVote($id, $user->id, $data['vote_type']);

        if (!$result['success']) {
            return $this->respondError($result['message'], [], 400);
        }

        return $this->respondSuccess($result, $result['message']);
    }

    /**
     * Get user's questions
     */
    public function getMyQuestions(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $perPage = $request->get('per_page', 10);
        $questions = $this->questionService->getUserQuestions($user->id, $perPage);

        $questions = AppQaQuestionResource::collection($questions);

        return $this->respondPaginated($questions, 'Your questions retrieved successfully');
    }

    /**
     * Get user's answers
     */
    public function getMyAnswers(Request $request)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return $this->respondUnauthorized();
        }

        $perPage = $request->get('per_page', 10);
        $answers = $this->answerService->getUserAnswers($user->id, $perPage);

        $answers = AppQaAnswerResource::collection($answers);
        
        return $this->respondPaginated($answers, 'Your answers retrieved successfully');
    }
}
