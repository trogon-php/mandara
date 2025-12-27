<?php

namespace App\Services\App;

use App\Services\App\AppBaseService;
use App\Services\BabySizeComparisons\BabySizeComparisonService;

class AppBabyComparisonService extends AppBaseService
{
    protected string $cachePrefix = 'baby-size-comparison';
    protected int $defaultTtl = 3600; // Cache for 1 hour since this is reference data

    public function __construct(protected BabySizeComparisonService $babySizeComparisonService) {}

    /**
     * Get baby size comparison by week
     */
    public function getByWeek(int $week): ?array
    {
        // Ensure week is between 1 and 40
        $week = max(1, min(40, $week));
        
        return $this->remember("week:{$week}", function () use ($week) {
            // Query the database for the baby size comparison for this week
            $comparison = $this->babySizeComparisonService->getByWeek($week);

            return $comparison;
        });
    }
}
