<?php

namespace App\Services\UserPeriodHistory;

use App\Models\UserPeriodHistory;
use App\Services\Core\BaseService;
use App\Services\Users\UserMetaService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserPeriodHistoryService extends BaseService
{
    protected string $modelClass = UserPeriodHistory::class;

    public function __construct(
        protected UserMetaService $userMetaService
    ) {
        parent::__construct();
    }

    /**
     * Add a new period entry
     */
    public function addPeriod(int $userId, string $startDate, ?int $periodLength = null): Model
    {
        // Get previous period to calculate cycle length
        $previousPeriod = $this->getLatestPeriod($userId);
        $cycleLength = null;
        
        if ($previousPeriod) {
            $cycleLength = Carbon::parse($previousPeriod->start_date)
                ->diffInDays(Carbon::parse($startDate));
        }

        // Create new period entry
        $period = $this->store([
            'user_id' => $userId,
            'start_date' => $startDate,
            'period_length' => $periodLength,
            'cycle_length' => $cycleLength,
        ]);

        // Update user's last_period_date
        $this->userMetaService->updateUserMetaValue($userId, 'last_period_date', $startDate);

        // Recalculate average cycle length
        $this->updateAvgCycleLength($userId);

        return $period;
    }

    /**
     * Get latest period for a user
     */
    public function getLatestPeriod(int $userId): ?Model
    {
        return $this->model
            ->forUser($userId)
            ->latestFirst()
            ->first();
    }

    /**
     * Get all periods for a user
     */
    public function getUserPeriods(int $userId, ?int $limit = null): Collection
    {
        $query = $this->model->forUser($userId)->latestFirst();
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Update average cycle length in user meta
     */
    private function updateAvgCycleLength(int $userId): void
    {
        $periods = $this->getUserPeriods($userId);
        
        if ($periods->count() < 2) {
            return; // Need at least 2 periods to calculate average
        }

        $cycleLengths = [];
        $periodsArray = $periods->sortBy('start_date')->values();
        
        for ($i = 1; $i < $periodsArray->count(); $i++) {
            $prev = Carbon::parse($periodsArray[$i-1]->start_date);
            $curr = Carbon::parse($periodsArray[$i]->start_date);
            $cycleLengths[] = $prev->diffInDays($curr);
        }

        if (!empty($cycleLengths)) {
            $avgCycle = round(array_sum($cycleLengths) / count($cycleLengths));
            $this->userMetaService->updateUserMetaValue($userId, 'avg_cycle_length', $avgCycle);
        }
    }

    /**
     * Get period history statistics
     */
    public function getPeriodStats(int $userId): array
    {
        $periods = $this->getUserPeriods($userId);
        
        if ($periods->isEmpty()) {
            return [
                'total_periods' => 0,
                'avg_cycle_length' => 28,
                'avg_period_length' => 5,
                'regularity' => 'Not enough data',
            ];
        }

        $avgCycleLength = (int) $this->userMetaService->getUserMetaValue($userId, 'avg_cycle_length', 28);
        
        $periodLengths = $periods->whereNotNull('period_length')
            ->pluck('period_length')
            ->toArray();
        
        $avgPeriodLength = !empty($periodLengths) 
            ? round(array_sum($periodLengths) / count($periodLengths))
            : 5;

        // Calculate regularity
        $regularity = 'Regular & Predictable';
        if ($periods->count() >= 3) {
            $cycleLengths = [];
            $sorted = $periods->sortBy('start_date')->values();
            for ($i = 1; $i < $sorted->count(); $i++) {
                $cycleLengths[] = Carbon::parse($sorted[$i-1]->start_date)
                    ->diffInDays(Carbon::parse($sorted[$i]->start_date));
            }
            
            if (!empty($cycleLengths)) {
                $variance = $this->calculateVariance($cycleLengths);
                if ($variance > 7) {
                    $regularity = 'Irregular';
                }
            }
        } else {
            $regularity = 'Tracking...';
        }

        return [
            'total_periods' => $periods->count(),
            'avg_cycle_length' => $avgCycleLength,
            'avg_period_length' => $avgPeriodLength,
            'regularity' => $regularity,
        ];
    }

    /**
     * Calculate variance of cycle lengths
     */
    private function calculateVariance(array $values): float
    {
        $mean = array_sum($values) / count($values);
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        return sqrt($variance / count($values));
    }

    // Required abstract methods
    public function getFilterConfig(): array
    {
        return [
            'user_id' => [
                'type' => 'exact',
                'label' => 'User ID',
            ],
        ];
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
        return [
            'field' => 'start_date',
            'direction' => 'desc',
        ];
    }
}
