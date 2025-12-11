<?php

namespace App\Services\Qa;

use App\Models\QaVote;
use App\Services\Core\BaseService;
use Illuminate\Support\Collection;

class QaVoteService extends BaseService
{
    protected string $modelClass = QaVote::class;

    public function __construct(protected QaQuestionService $questionService)
    {
        parent::__construct();
    }
    public function getFilterConfig(): array
    {
        return [];
    }
    public function getSearchFieldsConfig(): array
    {
        return [];
    }
    public function getDefaultSearchFields(): array
    {
        return [];
    }
    public function getDefaultSorting(): array
    {
        return [];
    }

    // ==================== API METHODS ====================

    /**
     * Vote on a question (helpful/not helpful) - API
     */
    public function storeOrUpdateVote(int $questionId, int $userId, string $voteType): array
    {
        // Validate vote type
        if (!in_array($voteType, ['helpful', 'not_helpful', 'none'])) {
            return [
                'success' => false,
                'message' => 'Invalid vote type',
            ];
        }

        // Check if user already voted
        $existingVote = $this->model
            ->where('question_id', $questionId)
            ->where('user_id', $userId)
            ->first();

        if ($existingVote) {
            if($voteType === 'none') {
                $existingVote->delete();
                return [
                    'success' => true,
                    'message' => 'Vote removed',
                    'action' => 'removed',
                    'vote' => null,
                ];
            }
            // Update existing vote
            $existingVote->vote_type = $voteType;
            $existingVote->save();
            return [
                'success' => true,
                'message' => 'Vote updated',
                'action' => 'updated',
                'vote' => $existingVote,
            ];
        } else {
            if($voteType === 'none') {
                return [
                    'success' => true,
                    'message' => 'Vote removed',
                    'action' => 'removed',
                    'vote' => null,
                ];
            }
            // Create new vote
            $vote = parent::store([
                'question_id' => $questionId,
                'user_id' => $userId,
                'vote_type' => $voteType,
            ]);
            
            return [
                'success' => true,
                'message' => 'Vote added',
                'action' => 'added',
                'vote' => $vote,
            ];
        }
    }

    /**
     * Get user's vote on a question - API
     */
    public function getUserVote(int $questionId, int $userId): ?QaVote
    {
        return $this->model
            ->where('question_id', $questionId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get vote counts for a question
     */
    public function getVoteCounts(int $questionId): array
    {
        return [
            'helpful' => $this->model
                ->where('question_id', $questionId)
                ->where('vote_type', 'helpful')
                ->count(),
            'not_helpful' => $this->model
                ->where('question_id', $questionId)
                ->where('vote_type', 'not_helpful')
                ->count(),
        ];
    }

    /**
     * Get all votes for a question
     */
    public function getQuestionVotes(int $questionId): Collection
    {
        return $this->model
            ->where('question_id', $questionId)
            ->with('user')
            ->get();
    }

    /**
     * Check if user has voted on question
     */
    public function hasUserVoted(int $questionId, int $userId): bool
    {
        return $this->model
            ->where('question_id', $questionId)
            ->where('user_id', $userId)
            ->exists();
    }
}
