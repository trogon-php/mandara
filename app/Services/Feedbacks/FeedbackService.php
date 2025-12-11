<?php

namespace App\Services\Feedbacks;

use App\Models\Feedback;
use App\Services\Core\BaseService;
use Illuminate\Database\Eloquent\Model;

class FeedbackService extends BaseService
{
    protected string $modelClass = Feedback::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all feedbacks with user relationship
     */
    public function getAll(): \Illuminate\Support\Collection
    {
        return $this->model->with('user')->sorted()->get();
    }

    /**
     * Get feedbacks with pagination and filters
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->with(['user']);

        // Apply search
        if (!empty($params['search'])) {
            $this->applySearch($query, $params['search']);
        }

        // Apply filters
        if (!empty($params['filters'])) {
            $this->applyFilters($query, $params['filters']);
        }

        // Apply sorting
        $this->applySorting($query, $params['sort_by'] ?? null, $params['sort_dir'] ?? 'desc');

        // Apply pagination if requested
        if (isset($params['paginate']) && $params['paginate']) {
            return $query->paginate($params['per_page'] ?? 15);
        }

        return $query->get();
    }

    /**
     * Get feedbacks for a specific user
     */
    public function getUserFeedbacks(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get single feedback for a specific user
     */
    public function getUserFeedback(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Create feedback for a user
     */
    public function createUserFeedback(array $data): Model
    {
        $data['status'] = 'pending'; // Always start as pending
        return parent::store($data);
    }

    /**
     * Create or update feedback for a user (upsert)
     */
    public function upsertUserFeedback(array $data): Model
    {
        $data['status'] = 'pending'; // Always start as pending
        
        // Check if user already has feedback
        $existingFeedback = $this->model->where('user_id', $data['user_id'])->first();
        
        if ($existingFeedback) {
            // Update existing feedback
            $existingFeedback->update($data);
            return $existingFeedback;
        } else {
            // Create new feedback
            return parent::store($data);
        }
    }

    /**
     * Update feedback status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $feedback = $this->find($id);
        if (!$feedback) {
            return false;
        }

        $feedback->status = $status;
        return $feedback->save();
    }

    /**
     * Get feedback statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'pending' => $this->model->pending()->count(),
            'reviewed' => $this->model->reviewed()->count(),
            'resolved' => $this->model->resolved()->count(),
            'with_rating' => $this->model->withRating()->count(),
            'average_rating' => round($this->model->withRating()->avg('rating') ?? 0, 2),
        ];
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
                    'pending' => 'Pending',
                    'reviewed' => 'Reviewed',
                    'resolved' => 'Resolved',
                ],
            ],
            'rating' => [
                'type' => 'select',
                'label' => 'Rating',
                'col' => 3,
                'options' => [
                    '1' => '1 Star',
                    '2' => '2 Stars',
                    '3' => '3 Stars',
                    '4' => '4 Stars',
                    '5' => '5 Stars',
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
            'user.name' => 'User Name',
            'user.email' => 'User Email',
            'user.phone' => 'User Phone',
            'message' => 'Feedback Message',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['user.name', 'user.email', 'message'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    /**
     * Apply search to query
     */
    protected function applySearch($query, string $search)
    {
        $searchFields = $this->getDefaultSearchFields();
        
        $query->where(function ($q) use ($search, $searchFields) {
            foreach ($searchFields as $field) {
                if (str_contains($field, '.')) {
                    // Handle relationship fields
                    [$relation, $column] = explode('.', $field);
                    $q->orWhereHas($relation, function ($subQuery) use ($column, $search) {
                        $subQuery->where($column, 'like', "%{$search}%");
                    });
                } else {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            }
        });
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                if ($field === 'rating') {
                    $query->where('rating', $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting($query, ?string $sortBy = null, string $sortDir = 'asc')
    {
        if ($sortBy) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $defaultSorting = $this->getDefaultSorting();
            $query->orderBy($defaultSorting['field'], $defaultSorting['direction']);
        }
    }
}
