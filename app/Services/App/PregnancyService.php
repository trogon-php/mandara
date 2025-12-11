<?php

namespace App\Services\App;

use App\Services\Users\UserMetaService;
use Carbon\Carbon;

class PregnancyService extends AppBaseService
{
    protected string $cachePrefix = 'pregnancy';
    protected int $defaultTtl = 300;

    public function __construct(
        protected UserMetaService $userMetaService
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
                $progressPercentage = min(100, max(0, ($daysPregnant / $totalDays) * 100));
                
                // Get baby size comparison - ensure week is valid
                $validWeek = max(1, min(40, $weeks)); // Clamp between 1 and 40
                $babyComparison = $this->getBabySizeComparison($validWeek);
                
                return [
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

    /**
     * Get baby size comparison items based on pregnancy week
     */
    private function getBabySizeComparison(int $week): array
    {
        // Ensure week is between 1 and 40
        $week = max(1, min(40, $week));
        
        // Baby size comparison mapping based on weeks
        // This is a simplified mapping - you can expand this based on actual baby size data
        $comparisons = [
            // Weeks 1-12: Early pregnancy (small items)
            ['name' => 'Poppy Seed', 'image' => 'poppy_seed.png'],
            ['name' => 'Blueberry', 'image' => 'blueberry.png'],
            ['name' => 'Grape', 'image' => 'grape.png'],
            ['name' => 'Olive', 'image' => 'olive.png'],
            ['name' => 'Lime', 'image' => 'lime.png'],
            ['name' => 'Plum', 'image' => 'plum.png'],
            ['name' => 'Peach', 'image' => 'peach.png'],
            ['name' => 'Lemon', 'image' => 'lemon.png'],
            ['name' => 'Apple', 'image' => 'apple.png'],
            ['name' => 'Orange', 'image' => 'orange.png'],
            ['name' => 'Avocado', 'image' => 'avocado.png'],
            ['name' => 'Banana', 'image' => 'banana.png'],
            
            // Weeks 13-24: Mid pregnancy (medium items)
            ['name' => 'Peach', 'image' => 'peach.png'],
            ['name' => 'Apple', 'image' => 'apple.png'],
            ['name' => 'Orange', 'image' => 'orange.png'],
            ['name' => 'Avocado', 'image' => 'avocado.png'],
            ['name' => 'Banana', 'image' => 'banana.png'],
            ['name' => 'Mango', 'image' => 'mango.png'],
            ['name' => 'Bell Pepper', 'image' => 'bell_pepper.png'],
            ['name' => 'Cucumber', 'image' => 'cucumber.png'],
            ['name' => 'Carrot', 'image' => 'carrot.png'],
            ['name' => 'Corn', 'image' => 'corn.png'],
            ['name' => 'Pomegranate', 'image' => 'pomegranate.png'],
            ['name' => 'Grapefruit', 'image' => 'grapefruit.png'],
            
            // Weeks 25-36: Late pregnancy (larger items)
            ['name' => 'Cauliflower', 'image' => 'cauliflower.png'],
            ['name' => 'Eggplant', 'image' => 'eggplant.png'],
            ['name' => 'Cabbage', 'image' => 'cabbage.png'],
            ['name' => 'Coconut', 'image' => 'coconut.png'],
            ['name' => 'Pineapple', 'image' => 'pineapple.png'],
            ['name' => 'Cantaloupe', 'image' => 'cantaloupe.png'],
            ['name' => 'Honeydew', 'image' => 'honeydew.png'],
            ['name' => 'Watermelon', 'image' => 'watermelon.png'],
            ['name' => 'Pumpkin', 'image' => 'pumpkin.png'],
            ['name' => 'Butternut Squash', 'image' => 'butternut_squash.png'],
            ['name' => 'Turtle', 'image' => 'turtle.png'],
            ['name' => 'Cake', 'image' => 'cake.png'],
            
            // Weeks 37-40: Full term (largest items)
            ['name' => 'Turtle', 'image' => 'turtle.png'],
            ['name' => 'Cake', 'image' => 'cake.png'],
            ['name' => 'Watermelon', 'image' => 'watermelon.png'],
            ['name' => 'Pumpkin', 'image' => 'pumpkin.png'],
        ];

        // For weeks 37-40, return Apple, Turtle, Cake as shown in screenshot
        if ($week >= 37 && $week <= 40) {
            return [
                ['name' => 'Apple', 'image' => 'apple.png'],
                ['name' => 'Turtle', 'image' => 'turtle.png'],
                ['name' => 'Cake', 'image' => 'cake.png'],
            ];
        }
        
        // Get comparison for current week (0-indexed, so week 1 = index 0)
        $index = max(0, min($week - 1, count($comparisons) - 1));
        $currentComparison = $comparisons[$index];
        
        // For other weeks, return current week comparison and two adjacent ones
        $startIndex = max(0, $index - 1);
        $endIndex = min(count($comparisons) - 1, $index + 1);
        
        $result = [];
        for ($i = $startIndex; $i <= $endIndex && count($result) < 3; $i++) {
            $result[] = $comparisons[$i];
        }
        
        // If we don't have 3 items, pad with the current one
        while (count($result) < 3) {
            $result[] = $currentComparison;
        }
        
        return array_slice($result, 0, 3);
    }
}
