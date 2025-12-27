<?php

namespace App\Services\MandaraBookings;

use App\Models\MandaraBooking;
use App\Services\Core\BaseService;


class MandaraBookingService extends BaseService
{
    protected string $modelClass = MandaraBooking::class;

    public function getFilterConfig(): array
    {
        return [
            'approval_status' => [
                'type' => 'exact',
                'label' => 'Approval Status',
                'col' => 3,
                'options' => [
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ],
            ]
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'booking_number' => 'Booking Number',
            'user.name' => 'User Name',
            'approval_status' => 'Approval Status',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['booking_number', 'user.name', 'date_from', 'date_to', 'approval_status'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    public function getByUserId(int $userId): ?MandaraBooking
    {
        return $this->model->where('user_id', $userId)->first();
    }
}