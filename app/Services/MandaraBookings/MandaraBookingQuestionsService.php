<?php

namespace App\Services\MandaraBookings;

use App\Models\MandaraBookingQuestion;
use App\Services\Core\BaseService;

class MandaraBookingQuestionsService extends BaseService
{
    protected string $modelClass = MandaraBookingQuestion::class;


    public function getFilterConfig(): array
    {
        return [
            'question' => [
                'type' => 'exact',
                'label' => 'Question',
                'col' => 3,
            ]
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'question' => 'Question',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['question'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }
    /**
     * Get all active questions (ordered)
     */
    public function getForAdditionalDetails()
    {
        return $this->model
            ->orderBy('id')
            ->get();
    }

}