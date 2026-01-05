<?php

namespace App\Services\Amenities;

use App\Models\AmenityBooking;
use App\Services\Core\BaseService;
use App\Services\Amenities\AmenityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AmenityBookingService extends BaseService
{
    protected string $modelClass = AmenityBooking::class;

    public function __construct(
        private AmenityService $amenityService
    ) {
        parent::__construct();
    }

    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'exact',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'upcoming' => 'Upcoming',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'no_show' => 'No Show',
                ],
            ],
            'payment_status' => [
                'type' => 'exact',
                'label' => 'Payment Status',
                'col' => 3,
                'options' => [
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ],
            ],
            'amenity_id' => [
                'type' => 'exact',
                'label' => 'Amenity',
                'col' => 3,
                'options' => $this->amenityService->getAmenityOptions(),
            ],
            'booking_date' => [
                'type' => 'date_range',
                'label' => 'Booking Date',
                'col' => 6,
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'booking_number' => 'Booking Number',
            'user.name' => 'User Name',
            'user.phone' => 'User Phone',
            'amenity.title' => 'Amenity',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['booking_number', 'user.name', 'user.phone', 'amenity.title'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }
    /**
     * Create a booking with items
     */
    public function createBookingWithItems(array $bookingData, array $itemsData): AmenityBooking
    {
        return DB::transaction(function () use ($bookingData, $itemsData) {
            // Create the booking
            $booking = $this->store($bookingData);
            
            // Create booking items
            foreach ($itemsData as $index => $itemData) {
                \App\Models\AmenityBookingItem::create([
                    'amenity_booking_id' => $booking->id,
                    'amenity_item_id' => $itemData['amenity_item_id'],
                    'item_title' => $itemData['item_title'],
                    'duration_minutes' => $itemData['duration_minutes'],
                    'was_included_in_package' => $itemData['was_included_in_package'],
                    'price' => $itemData['price'],
                    'sort_order' => $index + 1,
                ]);
            }
            
            // Calculate and update end time
            $booking->calculateEndTime();
            
            return $booking->fresh(['items', 'amenity']);
        });
    }

    /**
     * Get bookings for a specific user
     */
    public function getBookingsByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)
            ->with(['amenity', 'items.amenityItem'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->get();
    }

    /**
     * Get upcoming bookings
     */
    public function getUpcomingBookings()
    {
        return $this->model->upcoming()
            ->with(['user', 'amenity', 'items'])
            ->get();
    }

    /**
     * Get bookings for a specific date
     */
    public function getBookingsForDate(string $date)
    {
        return $this->model->forDate($date)
            ->with(['user', 'amenity', 'items'])
            ->orderBy('booking_time')
            ->get();
    }

    /**
     * Confirm a booking
     */
    public function confirmBooking(int $id): bool
    {
        $booking = $this->find($id);
        if (!$booking) {
            return false;
        }

        $booking->update(['status' => 'confirmed']);
        return true;
    }

    /**
     * Complete a booking
     */
    public function completeBooking(int $id): bool
    {
        $booking = $this->find($id);
        if (!$booking) {
            return false;
        }

        $booking->update(['status' => 'completed']);
        return true;
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(int $id, ?string $reason = null): bool
    {
        $booking = $this->find($id);
        if (!$booking) {
            return false;
        }

        $booking->cancel($reason);
        return true;
    }
}