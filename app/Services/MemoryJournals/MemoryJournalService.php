<?php

namespace App\Services\MemoryJournals;

use App\Models\MemoryJournal;
use App\Services\Core\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MemoryJournalService extends BaseService
{
    protected string $modelClass = MemoryJournal::class;

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
            'user_id' => [
                'type' => 'select',
                'label' => 'User',
                'col' => 3,
                'options' => $this->getUserOptions(),
            ],
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'content' => 'Content',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['content'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'date', 'direction' => 'desc'];
    }

    /**
     * Get user options for filters
     */
    private function getUserOptions(): array
    {
        $users = \App\Models\User::orderBy('name')->get();
        $options = ['' => 'All Users'];
        
        foreach ($users as $user) {
            $options[$user->id] = $user->name;
        }
        
        return $options;
    }

    // ==================== API METHODS ====================

    /**
     * Get paginated memory journals for a user (API)
     */
    public function getUserMemoryJournals(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('user_id', $userId)
            ->with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get memory journal by ID for API
     */
    public function getMemoryJournalForApi(int $id, ?int $userId = null): ?MemoryJournal
    {
        $query = $this->model->with('user');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->find($id);
    }

    /**
     * Create memory journal by user (API)
     */
    public function createUserMemoryJournal(array $data): MemoryJournal
    {
        return parent::store($data);
    }

    /**
     * Update user's memory journal (API)
     */
    public function updateUserMemoryJournal(int $journalId, int $userId, array $data): ?MemoryJournal
    {
        $journal = $this->model
            ->where('id', $journalId)
            ->where('user_id', $userId)
            ->first();

        if (!$journal) {
            return null;
        }

        $updated = parent::update($journalId, $data);

        return $updated ? $updated->fresh(['user']) : null;
    }

    /**
     * Delete user's memory journal (API)
     */
    public function deleteUserMemoryJournal(int $journalId, int $userId): bool
    {
        $journal = $this->model
            ->where('id', $journalId)
            ->where('user_id', $userId)
            ->first();

        if (!$journal) {
            return false;
        }

        return parent::delete($journalId);
    }

    /**
     * Get memory journals by date range for API
     */
    public function getMemoryJournalsByDateRange(int $userId, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('user')
            ->orderBy('date', 'desc')
            ->get();
    }
}