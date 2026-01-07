<?php

namespace App\Services\App;

use App\Services\BabySizeComparisons\BabySizeComparisonService;
use App\Services\UserPeriodHistory\UserPeriodHistoryService;
use App\Services\Users\UserMetaService;
use Carbon\Carbon;

class UserJourneyService extends AppBaseService
{
    protected string $cachePrefix = 'userjourney';
    protected int $defaultTtl = 300;
    protected int $cycleLength = 28;

    public function __construct(
        protected UserMetaService $userMetaService,
        protected BabySizeComparisonService $babySizeComparisonService,
        protected UserPeriodHistoryService $periodHistoryService
    ) {}

    /**
     * Get pregnancy progress data for the authenticated user
     */
    public function getPregnancyProgress(?int $userId = null): ?array
    {
        if (!$userId) {
            $user = $this->getAuthUser();
            if (!$user) {
                return null;
            }
            $userId = $user->id;
        }

        return $this->remember("user:{$userId}", function () use ($userId) {
            // Get pregnancy-related user meta
            $isPregnant = $this->userMetaService->getUserMetaValue($userId, 'is_pregnant', 0);
            $deliveryDate = $this->userMetaService->getUserMetaValue($userId, 'delivery_date');

            // Check if user is pregnant and has delivery date
            if (!$isPregnant || !$deliveryDate) {
                return null;
            }

            try {
                $deliveryDate = Carbon::parse($deliveryDate);
                $today = Carbon::today();
                
                // Calculate pregnancy start date (typically 40 weeks before delivery)
                $pregnancyStartDate = $deliveryDate->copy()->subWeeks(40);
                
                // Calculate current week and days - ensure we get positive value
                // Use the earlier date first to ensure positive result
                if ($pregnancyStartDate->isBefore($today)) {
                    $daysPregnant = $pregnancyStartDate->diffInDays($today);
                } else {
                    // If pregnancy hasn't started yet, return 0
                    $daysPregnant = 0;
                }
                
                $weeks = floor($daysPregnant / 7);
                $days = $daysPregnant % 7;
                
                // Ensure weeks don't exceed 40
                if ($weeks > 40) {
                    $weeks = 40;
                    $days = 0;
                }
                
                // Ensure weeks and days are not negative
                if ($weeks < 0) {
                    $weeks = 0;
                }
                if ($days < 0) {
                    $days = 0;
                }
                
                // Calculate days until delivery
                $daysUntilDelivery = $today->diffInDays($deliveryDate, false);
                if ($daysUntilDelivery < 0) {
                    $daysUntilDelivery = 0; // Already past due date
                }
                
                // Calculate progress percentage (0-100%)
                $totalDays = 40 * 7; // 280 days
                $progressPercentage = round(min(100, max(0, ($daysPregnant / $totalDays) * 100)));
                
                // Get baby size comparison - ensure week is valid
                $validWeek = max(1, min(40, $weeks)); // Clamp between 1 and 40
                $babyComparison = $this->babySizeComparisonService->getBabySizeComparison($validWeek);
                
                return [
                    'delivery_date' => $deliveryDate->format('Y-m-d'),
                    'weeks' => $weeks,
                    'days' => $days,
                    'weeks_text' => "{$weeks} weeks & {$days} Days",
                    'days_until_delivery' => $daysUntilDelivery,
                    'days_until_delivery_text' => $daysUntilDelivery > 0 
                        ? "Due in {$daysUntilDelivery} " . ($daysUntilDelivery == 1 ? 'Day' : 'Days')
                        : ($daysUntilDelivery == 0 ? 'Due Today' : 'Past Due'),
                    'progress_percentage' => round($progressPercentage, 2),
                    'current_week' => $weeks,
                    'total_weeks' => 40,
                    'baby_comparison' => $babyComparison,
                ];
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    // calculate periods cycle
    public function calculatePeriodCycle(string $lastPeriodDate): array
    {
        $cycleLength = 28;

        $last = Carbon::parse($lastPeriodDate);
        $today = Carbon::today();

        return [
            'last_period_date' => $last->format('Y-m-d'),
            'day' => "Day " . ($last->diffInDays($today) + 1),
            'milestones' => [
                'last_period_day' => $last->format('d'),
                'last_period_month' => $last->format('M'),
                'ovulation_day' => $last->copy()->addDays($cycleLength - 14)->format('d'),
                'ovulation_month' => $last->copy()->addDays($cycleLength - 14)->format('M'),
                'next_period_day' => $last->copy()->addDays($cycleLength)->format('d'),
                'next_period_month' => $last->copy()->addDays($cycleLength)->format('M'),
            ],
        ];
    }

    public function postpartumTimeline(string $babyDob): array
    {
        $dob = Carbon::parse($babyDob)->startOfDay();
        $today = Carbon::today();

        // Total recovery
        $totalWeeks = 40;
        $endDate = $dob->copy()->addWeeks($totalWeeks);

        // Current week (min 1, max 40)
        $weeksPassed = max(1, $dob->diffInWeeks($today) + 1);
        $currentWeek = intval(min($weeksPassed, $totalWeeks));

        // Progress percentage of how much days passed %
        $progress = round(($dob->diffInDays($today) / ($totalWeeks * 7)) * 100);

        $remainingWeeks = $totalWeeks - $currentWeek;
        $remainingDays = 280 - $dob->diffInDays($today);

        return [
            'baby_dob'          => $dob->format('Y-m-d'),
            'current_week'      => $currentWeek,
            'total_weeks'       => $totalWeeks,
            'week_label'        => "{$currentWeek} Weeks Postpartum",
            'start_week'        => 1,
            'end_week'          => 40,
            'days_since'        => $dob->diffInDays($today),
            'remaining'         => $remainingWeeks > 0 ? $remainingWeeks : $remainingDays,// weeks or days
            'remaining_label'   => $totalWeeks - $currentWeek > 0 ? 'wks' : 'days',
            'recovery_end_date' => $endDate->toDateString(),
            'progress_percent'  => $progress,
            'stage'             => $this->stageLabel($currentWeek),
            "today_focus" => [
                "title" =>"Gentle Recovery & Baby Bonding",
                "content" => "Focus on rest and light movement today. Short walks and gentle stretching can support healing. Stay hydrated, eat nourishing meals, and spend calm, uninterrupted time bonding with your baby."
            ]
        ];
    }

    private function stageLabel(int $week): string
    {
        return match (true) {
            $week <= 6   => 'Baby Care and Recovery',
            $week <= 12  => 'Physical Healing Phase',
            $week <= 24  => 'Strength & Routine Building',
            default      => 'Full Recovery & Adjustment',
        };
    }

    public function getRecoveryJourneyData(): array
    {
        return [
            [
                'title' => 'Physical Rest & Healing',
                'description' => 'Focus on Sleep, and allowing your body to recover from delivery',
            ],
            [
                'title' => 'Physical Rest & Healing',
                'description' => 'Focus on Sleep, and allowing your body to recover from delivery',
            ],
            [
                'title' => 'Physical Rest & Healing',
                'description' => 'Focus on Sleep, and allowing your body to recover from delivery',
            ],
        ];
    }
    public function getWellnessGuideData(): array
    {
        return [
            [
                'image' => 'https://mandara-files.trogon.info/app/uploads/media/69521df434aad.png',
                'title' => 'Physical',
                'description' => 'Focus on rest, nutrition and gentle movement, Listen to your body',
            ],
            [
                'image' => 'https://mandara-files.trogon.info/app/uploads/media/69521df3daca2.png',
                'title' => 'Mental',
                'description' => 'Acknowlege your feelings and seek support when needed',
            ],
            [
                'image' => 'https://mandara-files.trogon.info/app/uploads/media/69521df399699.png',
                'title' => 'Social',
                'description' => 'Stay connected with your support network and community',
            ],
            [
                'image' => 'https://mandara-files.trogon.info/app/uploads/media/69521df2a07c9.png',
                'title' => 'Medical',
                'description' => 'Attend checkups and contact your provider with concerns',
            ],
        ];
    }

    public function getWeeklyTipsData(int $currentWeek): array
    {
        switch($currentWeek) {
            case 1:
                return [
                    'title' => 'Week 1',
                    'description' => 'Focus on rest, nutrition and gentle movement, Listen to your body',
                ];
            case 2:
                return [
                    'title' => 'Week 2',
                    'description' => 'Focus on rest, nutrition and gentle movement, Listen to your body',
                ];
            default:
                return [
                    'title' => 'Week ' . $currentWeek,
                    'description' => 'Focus on rest, nutrition and gentle movement, Listen to your body',
                ];
        }
    }
    public function getFertilityOverview(string $lastPeriodDate): array
    {
        $cycleLength = $this->cycleLength;
        $last = Carbon::parse($lastPeriodDate);
        $today = Carbon::today();

        $cycleProgress = round(($last->diffInDays($today) / ($cycleLength * 7)) * 100);

        return [
            'cycle_progress' => $cycleProgress,
            'next_period_date' => $last->copy()->addDays($cycleLength)->format('M d'),
            'since' => 'in ' . $last->diffInDays($today) . ' days',
        ];
    }
    public function calculateNextPeriodDate(string $lastPeriodDate, int $cycleLength): string
    {
        $last = Carbon::parse($lastPeriodDate);
        return $last->copy()->addDays($cycleLength)->format('Y-m-d');
    }
    public function calculateOvulationDate(string $lastPeriodDate, int $cycleLength): string
    {
        $last = Carbon::parse($lastPeriodDate);
        return $last->copy()->addDays($cycleLength - 14)->format('Y-m-d');
    }

    public function getCycleOverview(?int $userId = null): ?array
    {
        if (!$userId) {
            $user = $this->getAuthUser();
            if (!$user) return null;
            $userId = $user->id;
        }

        $lastPeriodDate = $this->userMetaService->getUserMetaValue($userId, 'last_period_date');
        if (!$lastPeriodDate) return null;

        $avgCycleLength = (int) $this->userMetaService->getUserMetaValue($userId, 'avg_cycle_length', 28);
        $stats = $this->periodHistoryService->getPeriodStats($userId);
        
        $lastPeriod = Carbon::parse($lastPeriodDate);
        $today = Carbon::today();
        $daysSinceLastPeriod = $lastPeriod->diffInDays($today);
        $currentDay = $daysSinceLastPeriod + 1;
        
        // Calculate next period date
        $nextPeriodDate = $lastPeriod->copy()->addDays($avgCycleLength);
        $daysUntilNextPeriod = $today->diffInDays($nextPeriodDate, false);
        
        // Calculate ovulation and fertile window
        $ovulationDate = $lastPeriod->copy()->addDays($avgCycleLength - 14);
        $fertileWindowStart = $ovulationDate->copy()->subDays(5);
        $fertileWindowEnd = $ovulationDate->copy()->addDays(1);

        // Calculate cycle progress (0-100%)
        $cycleProgress = min(100, max(0, round(($daysSinceLastPeriod / $avgCycleLength) * 100)));

        // Check if popup should be shown
        $showPeriodPopup = false;
        if ($today->greaterThanOrEqualTo($nextPeriodDate)) {
            // Check if last_period_date is still before the expected next period date
            if ($lastPeriod->lessThan($nextPeriodDate)) {
                $showPeriodPopup = true;
            }
        }

        // Get latest period from history
        $latestPeriod = $this->periodHistoryService->getLatestPeriod($userId);
        $periodLength = $latestPeriod && $latestPeriod->period_length 
            ? $latestPeriod->period_length 
            : $stats['avg_period_length'];

        return [
            'current_date' => $today->format('Y-m-d'),
            'last_period_date' => $lastPeriodDate,
            'current_day' => $currentDay,
            'period_status' => $currentDay <= 7 ? 'Period' : 'Cycle',
            'cycle_progress' => $cycleProgress,
            'cycle_progress_text' => $cycleProgress < 5 ? 'Just getting started' : "",
            'next_period_date' => $nextPeriodDate->format('Y-m-d'),
            'next_period_display' => $nextPeriodDate->format('M d'),
            'days_until_next_period' => max(0, $daysUntilNextPeriod),
            'next_period_text' => $daysUntilNextPeriod > 0 
                ? "in {$daysUntilNextPeriod} " . ($daysUntilNextPeriod == 1 ? 'day' : 'days')
                : 'Overdue',
            'ovulation_date' => $ovulationDate->format('Y-m-d'),
            'fertile_window' => [
                'start' => $fertileWindowStart->format('Y-m-d'),
                'end' => $fertileWindowEnd->format('Y-m-d'),
                'display' => $fertileWindowStart->format('M d') . ' - ' . $fertileWindowEnd->format('M d'),
            ],
            'peak_fertility' => $ovulationDate->format('M d'),
            'period_report' => [
                'last_started' => $latestPeriod ? Carbon::parse($latestPeriod->start_date)->format('M d') : Carbon::parse($lastPeriodDate)->format('M d'),
                'period_length' => "{$periodLength} Days",
                'cycle_length' => "{$avgCycleLength} Days",
                'next_expected' => $nextPeriodDate->format('M d'),
                'status' => $daysUntilNextPeriod >= -2 ? 'On Track' : 'Irregular',
            ],
            'ovulation_report' => [
                'fertile_window' => $fertileWindowStart->format('M d') . ' - ' . $fertileWindowEnd->format('M d'),
                'peak_fertility' => $ovulationDate->format('M d'),
            ],
            'avg_cycle_length' => $avgCycleLength,
            'regularity' => $stats['regularity'],
            'show_period_popup' => $showPeriodPopup,
        ];
    }

    // Add confirmPeriodStarted method
    public function confirmPeriodStarted(string $startDate, ?int $periodLength, ?int $userId = null): bool
    {
        if (!$userId) {
            $user = $this->getAuthUser();
            if (!$user) return false;
            $userId = $user->id;
        }

        try {
            $this->periodHistoryService->addPeriod($userId, $startDate, $periodLength);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
