<?php

namespace App\Services\BabySizeComparisons;

use App\Models\BabySizeComparison;
use App\Services\Core\BaseService;

class BabySizeComparisonService extends BaseService
{
    protected $modelClass = BabySizeComparison::class;
    
    public function __construct() {
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
    /**
     * Get baby size comparison by week
     */
    public function getByWeek(int $week): ?array
    {
        // Ensure week is between 1 and 40
        $week = max(1, min(40, $week));
        // Query the database for the baby size comparison for this week
        $comparison = $this->model->where('week', (string)$week)->first();
            
        // If no record found, return null
        if (!$comparison) {
            return null;
        }
        
        // Return the full comparison data
        return [
            'week' => (int)$comparison->week,
            'comparisons' => [
                [
                    'name' => $comparison->comparison_one,
                    'image' => $comparison->comparison_one_url,
                ],
                [
                    'name' => $comparison->comparison_two,
                    'image' => $comparison->comparison_two_url,
                ],
                [
                    'name' => $comparison->comparison_three,
                    'image' => $comparison->comparison_three_url,
                ],
            ],
            'length' => (float)$comparison->length,
            'weight' => (float)$comparison->weight,
            'milestone_remarks' => $comparison->milestone_remarks,
        ];
    }
    /**
     * Get baby size comparison items based on pregnancy week
     */
    public function getBabySizeComparison(int $week): array
    {
        // Ensure week is between 1 and 40
        $week = max(1, min(40, $week));
        
        // Query the database for the baby size comparison for this week
        $comparison = $this->model->where('week', (string)$week)->first();
        
        // If no record found, return empty array or default values
        if (!$comparison) {
            return [];
        }
        
        // Transform database structure to match expected output format
        return [
            [
                'name' => $comparison->comparison_one,
                'image' => $comparison->comparison_one_url,
            ],
            [
                'name' => $comparison->comparison_two,
                'image' => $comparison->comparison_two_url,
            ],
            [
                'name' => $comparison->comparison_three,
                'image' => $comparison->comparison_three_url,
            ],
        ];
    }
}
