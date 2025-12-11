<?php

namespace App\Services\Qa;

use App\Models\QaAnswer;
use App\Services\Core\BaseService;
use App\Services\Traits\CacheableService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class QaAnswerService extends BaseService
{
    use CacheableService;
    
    protected string $cachePrefix = 'qa_answers';
    protected int $cacheTtl = 1800;
    
    protected string $modelClass = QaAnswer::class;

    public function __construct(protected QaQuestionService $questionService)
    {
        parent::__construct();
    }

    /**
     * Get filter configuration for admin
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ],
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'answer_text' => 'Answer Text',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['answer_text'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'asc'];
    }

    // ==================== API METHODS ====================

    /**
     * Get answers for a question (API)
     */
    public function getQuestionAnswers(int $questionId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->remember("question:answers:{$questionId}", function () use ($questionId, $perPage) {
            return $this->model
            ->where('question_id', $questionId)
            ->where('status', 1)
            ->with(['user', 'question'])
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
        });
    }

    /**
     * Get all answers for a question (no pagination) - API
     */
    public function getAllQuestionAnswers(int $questionId): Collection
    {
        return $this->model
            ->where('question_id', $questionId)
            ->where('status', 1)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get single answer for API
     */
    public function getAnswerForApi(int $answerId): ?QaAnswer
    {
        return $this->model
            ->where('id', $answerId)
            ->where('status', 1)
            ->with(['user', 'question'])
            ->first();
    }

    /**
     * Get user's answers for API
     */
    public function getUserAnswers(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['question.category'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get user's answer count
     */
    public function getUserAnswerCount(int $userId): int
    {
        return $this->model->where('user_id', $userId)->count();
    }

    /**
     * Create answer by user (API)
     */
    public function createUserAnswer(array $data): QaAnswer
    {
        $data['status'] = 1; // Auto-approve or set to 0 for moderation

        $answer = parent::store($data);

        return $answer->load(['user', 'question']);
    }

    /**
     * Update user's answer (API)
     */
    public function updateUserAnswer(int $answerId, int $userId, array $data): ?QaAnswer
    {
        $answer = $this->model
            ->where('id', $answerId)
            ->where('user_id', $userId)
            ->first();

        if (!$answer) {
            return null;
        }

        $answer->update($data);

        return $answer->fresh(['user', 'question']);
    }

    /**
     * Delete user's answer (API)
     */
    public function deleteUserAnswer(int $answerId, int $userId): bool
    {
        $answer = $this->model
            ->where('id', $answerId)
            ->where('user_id', $userId)
            ->first();

        if (!$answer) {
            return false;
        }

        $result = $answer->delete();

        return $result;
    }

    /**
     * Get answer count for a question
     */
    public function getAnswerCount(int $questionId): int
    {
        return $this->model
            ->where('question_id', $questionId)
            ->where('status', 1)
            ->count();
    }

    /**
     * Check if user has already answered a question
     */
    public function userHasAnswered(int $questionId, int $userId): bool
    {
        return $this->model
            ->where('question_id', $questionId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get answer statistics
     */
    public function getAnswerStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'published' => $this->model->where('status', 1)->count(),
        ];
    }
}
