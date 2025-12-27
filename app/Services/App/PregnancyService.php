<?php

namespace App\Services\App;

use App\Services\BabySizeComparisons\BabySizeComparisonService;
use App\Services\Users\UserMetaService;
use Carbon\Carbon;

class PregnancyService extends AppBaseService
{
    protected string $cachePrefix = 'pregnancy';
    protected int $defaultTtl = 300;

    public function __construct(
        protected UserMetaService $userMetaService,
        protected BabySizeComparisonService $babySizeComparisonService
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

        // Progress %
        $progress = round(($currentWeek / $totalWeeks) * 100);

        return [
            'baby_dob'          => $dob->format('Y-m-d'),
            'current_week'      => $currentWeek,
            'total_weeks'       => $totalWeeks,
            'week_label'        => "{$currentWeek} Weeks Postpartum",
            'start_week'        => 1,
            'end_week'          => 40,
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

    // public function getUserJourney(int $userId): string
    // {
    //     $preparingToConceive = $this->userMetaService->getUserMetaValue($userId, 'preparing_to_conceive');
    //     $isPregnant = $this->userMetaService->getUserMetaValue($userId, 'is_pregnant');
    //     $isDelivered = $this->userMetaService->getUserMetaValue($userId, 'is_delivered');

    //     if($preparingToConceive && $preparingToConceive == 1) {
    //         return 'preparing';
    //     }
    //     if($isPregnant && $isPregnant == 1) {
    //         return 'pregnant';
    //     }
    //     if($isDelivered && $isDelivered == 1) {
    //         return 'delivered';
    //     }
    //     return 'not determined';
    // }
}
