<?php

namespace App\Services\Qa;

use App\Models\QaCategory;
use App\Models\QaQuestion;
use App\Services\Core\BaseService;
use App\Services\Traits\CacheableService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class QaQuestionService extends BaseService
{
    use CacheableService;
    
    protected string $cachePrefix = 'qa_questions';
    protected int $cacheTtl = 1800;
    protected string $modelClass = QaQuestion::class;

    public function __construct()
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
            'qa_category_id' => [
                'type' => 'select',
                'label' => 'Category',
                'col' => 3,
                'options' => $this->getCategoryOptions(),
            ],
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'question_text' => 'Question Text',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'question_text'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    /**
     * Get category options for filters
     */
    private function getCategoryOptions(): array
    {
        $categories = QaCategory::where('status', 1)->orderBy('title')->get();
        $options = ['' => 'All Categories'];
        
        foreach ($categories as $category) {
            $options[$category->id] = $category->title;
        }
        
        return $options;
    }

    // ==================== API METHODS ====================

    /**
     * Get paginated published questions for API
     */
    public function getPublishedQuestionsPaginated(int $perPage = 10, ?int $categoryId = null): LengthAwarePaginator
    {
        $query = $this->model
            ->where('status', 1)
            ->with(['qaCategory', 'user', 'qaAnswers.user'])
            ->orderBy('created_at', 'desc');

        if ($categoryId) {
            $query->where('qa_category_id', $categoryId);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all published questions for API
     */
    public function getQuestionsPaginated(int $perPage = 10, ?int $categoryId = null): LengthAwarePaginator
    {
        $cacheKey = $categoryId ? "published:category:{$categoryId}" : 'published:all';
        
        return $this->remember($cacheKey, function () use ($categoryId, $perPage) {
            $query = $this->model
                ->where('status', 1)
                ->with(['category', 'user', 'answers.user','answers.question', 'votes'])
                ->orderBy('created_at', 'asc');

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            return $query->paginate($perPage);
        });
    }

    /**
     * Get single question with details for API
     */
    public function getQuestionForApi(int $id): ?QaQuestion
    {
        $question = $this->model
            ->where('status', 1)
            ->where('id', $id)
            ->with([
                'qaCategory',
                'user',
                'qaAnswers' => function($query) {
                    $query->where('status', 1)
                          ->with('user')
                          ->orderBy('created_at', 'asc');
                },
                'qaQuestionVotes'
            ])
            ->first();

        if ($question) {
            $this->incrementViewCount($id);
        }

        return $question;
    }

    /**
     * Get questions by category for API
     */
    public function getQuestionsByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('status', 1)
            ->where('category_id', $categoryId)
            ->with(['category', 'user', 'answers.user', 'votes'])
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Get user's questions for API
     */
    public function getUserQuestions(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['category', 'answers', 'votes'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get user's question count
     */
    public function getUserQuestionCount(int $userId): int
    {
        return $this->model->where('user_id', $userId)->count();
    }

    /**
     * Create question by user (API)
     */
    public function createUserQuestion(array $data): QaQuestion
    {

        $question = parent::store($data);
        
        // Clear cache
        $this->forget('published:all');
        $this->forget("published:category:{$data['category_id']}");

        return $question;
    }

    /**
     * Update user's question (API)
     */
    public function updateUserQuestion(int $questionId, int $userId, array $data): ?QaQuestion
    {
        $question = $this->model
            ->where('id', $questionId)
            ->where('user_id', $userId)
            ->first();

        if (!$question) {
            return null;
        }

        $question->update($data);
        
        // Clear cache
        $this->forget('published:all');
        $this->forget("published:category:{$question->category_id}");

        return $question->fresh(['category', 'user']);
    }

    /**
     * Delete user's question (API)
     */
    public function deleteUserQuestion(int $questionId, int $userId): bool
    {
        $question = $this->model
            ->where('id', $questionId)
            ->where('user_id', $userId)
            ->first();

        if (!$question) {
            return false;
        }

        $categoryId = $question->category_id;
        $result = $question->delete();
        
        // Clear cache
        $this->forget('published:all');
        $this->forget("published:category:{$categoryId}");

        return $result;
    }

    /**
     * Search questions for API
     */
    public function searchQuestions(string $searchTerm, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('status', 1)
            ->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('question_text', 'like', "%{$searchTerm}%");
            })
            ->with(['qaCategory', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get popular questions (by helpful count) for API
     */
    public function getPopularQuestions(int $limit = 10): Collection
    {
        return $this->remember("popular:{$limit}", function () use ($limit) {
            return $this->model
                ->where('status', 1)
                ->with(['qaCategory', 'user'])
                ->orderBy('helpful_count', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get recent questions for API
     */
    public function getRecentQuestions(int $limit = 10): Collection
    {
        return $this->remember("recent:{$limit}", function () use ($limit) {
            return $this->model
                ->where('status', 1)
                ->with(['qaCategory', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get questions with most answers for API
     */
    public function getMostAnsweredQuestions(int $limit = 10): Collection
    {
        return $this->remember("most_answered:{$limit}", function () use ($limit) {
            return $this->model
                ->where('status', 1)
                ->with(['qaCategory', 'user'])
                ->orderBy('answer_count', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(int $questionId): void
    {
        $this->model->where('id', $questionId)->increment('view_count');
    }

    /**
     * Update answer count (called when answer is added/deleted)
     */
    // public function updateAnswerCount(int $questionId): void
    // {
    //     $answerCount = \App\Models\QaAnswer::where('question_id', $questionId)
    //         ->where('status', 1)
    //         ->count();

    //     $this->model->where('id', $questionId)->update(['answer_count' => $answerCount]);
    // }

    /**
     * Update vote counts (called when vote is added/updated)
     */
    // public function updateVoteCounts(int $questionId): void
    // {
    //     $helpfulCount = \App\Models\QaQuestionVote::where('question_id', $questionId)
    //         ->where('vote_type', 'helpful')
    //         ->count();

    //     $notHelpfulCount = \App\Models\QaQuestionVote::where('question_id', $questionId)
    //         ->where('vote_type', 'not_helpful')
    //         ->count();

    //     $this->model->where('id', $questionId)->update([
    //         'helpful_count' => $helpfulCount,
    //         'not_helpful_count' => $notHelpfulCount,
    //     ]);
    // }

    /**
     * Get question statistics
     */
    public function getQuestionStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'published' => $this->model->where('status', 1)->count(),
            'total_answers' => \App\Models\QaAnswer::where('status', 1)->count(),
            'total_votes' => \App\Models\QaQuestionVote::count(),
        ];
    }
}
