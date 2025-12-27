<?php

namespace App\Http\Controllers\Api;

use App\Services\App\AppBabyComparisonService;
use Illuminate\Http\Request;

class BabySizeComparisonController extends BaseApiController
{
    public function __construct(protected AppBabyComparisonService $babySizeComparisonService) {}

    /**
     * Get baby size comparison by week
     */
    public function getByWeek(Request $request, int $week)
    {
        // Validate week is between 1 and 40
        if ($week < 1 || $week > 40) {
            return $this->respondValidationError(
                'Week must be between 1 and 40',
                ['week' => ['The week must be between 1 and 40.']]
            );
        }

        $data = $this->babySizeComparisonService->getByWeek($week);

        if ($data === null) {
            return $this->respondError(
                'Baby comparison data not found for this week',
                404
            );
        }

        return $this->respondSuccess(
            $data,
            'Baby comparison data fetched successfully'
        );
    }
}
