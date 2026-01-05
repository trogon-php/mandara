<?php

namespace App\Services\App;

use App\Http\Resources\Amenities\AppAmenityListResource;
use App\Services\Amenities\AmenityService;
use App\Services\Amenities\AmenityItemService;
use App\Services\Amenities\AmenityBookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AmenityAppService extends AppBaseService
{
    protected string $cachePrefix = 'amenity_app';
    protected int $defaultTtl = 300;

    public function __construct(
        private AmenityService $amenityService,
        private AmenityItemService $amenityItemService,
        private AmenityBookingService $amenityBookingService
    ) {
        $this->clearCache();
    }

    /**
     * List all amenities with basic information
     * 
     */
    public function listAmenities($perPage)
    {
        return $this->remember('amenities_list', function () use ($perPage) {
            $amenities = $this->amenityService->getAmenitiesPaginated($perPage);

            return AppAmenityListResource::collection($amenities);
        });
    }

    /**
     * Get amenity page data including bookings, items, dates, and times
     * 
     */
    public function amenityPage(int $amenityId): array
    {
        $user = $this->getAuthUser();
        $userId = $user ? $user->id : null;

        return $this->remember("amenity_page:{$amenityId}:user:{$userId}", function () use ($amenityId, $userId) {
            // Get amenity using service
            $amenity = $this->amenityService->find($amenityId);
            if (!$amenity) {
                return [
                    'status' => false,
                    'message' => 'Amenity not found',
                    'data' => []
                ];
            }

            // Get user's bookings for this amenity using service
            $bookings = [];
            if ($userId) {
                $userBookings = $this->amenityBookingService->getBookingsByUserId($userId)
                    ->where('amenity_id', $amenityId)
                    ->load('items')
                    ->values();

                $bookings = $userBookings->map(function ($booking) {
                    // Get all item titles from booking items
                    $itemTitles = $booking->items->pluck('item_title')->toArray();
                    
                    return [
                        'id' => $booking->id,
                        'booking_number' => $booking->booking_number,
                        'item_title' => implode(', ', $itemTitles), // Combined item titles
                        'status' => $booking->status,
                        'date' => $booking->booking_date->format('D, M d,Y'),
                        'time' => $booking->booking_time->format('h:i A'),
                        'duration' => $booking->totalDurationText(),
                        'items' => $booking->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'title' => $item->item_title,
                                'duration' => $item->duration_minutes,
                                'duration_text' =>  $item->duration_text,
                            ];
                        }),
                    ];
                })->toArray();
            }

            // Get amenity items with package inclusion status using service
            $itemsData = $this->amenityItemService->getItemsWithPackageStatus($amenityId, $userId);
            
            $items = $itemsData->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'duration' => $item['duration_minutes'],
                    'duration_text' => $item['duration_text'] ?? null,
                    'is_included' => $item['is_included'],
                    'price' => $item['is_included'] ? null : $item['price'], // Show price only if not included
                ];
            })->toArray();

            // Hardcoded date array (next 7 days)
            $dates = $this->getDateArray();

            // Hardcoded time array
            $times = $this->getTimeArray();

            return [
                'status' => true,
                'message' => 'Amenity page data retrieved successfully',
                'data' => [
                    'amenity' => [
                        'id' => $amenity->id,
                        'title' => $amenity->title,
                        'description' => $amenity->description,
                        'icon_url' => $amenity->icon_url,
                    ],
                    'bookings' => $bookings,
                    'items' => $items,
                    'dates' => $dates,
                    'times' => $times,
                    'empty_message' => $this->getBookingsEmptyMessageData($amenity->id),
                ]
            ];
        });
    }
    // Confirm booking
    public function createBooking(int $userId, array $data): array
    {
        $user = $this->getAuthUser();
        if (!$user || $user->id !== $userId) {
            return [
                'status' => false,
                'message' => 'User not authenticated',
                'data' => []
            ];
        }

        return DB::transaction(function () use ($userId, $data) {

            // Get amenity
            $amenity = $this->amenityService->find($data['amenity_id']);
            if (!$amenity) {
                return [
                    'status' => false,
                    'message' => 'Amenity not found',
                    'data' => []
                ];
            }

            // Get user's active package
            $userPackage = \App\Models\UserPackage::where('user_id', $userId)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>=', now());
                })
                ->first();

            // Get selected amenity items
            $amenityItemIds = is_array($data['amenity_item_ids']) ? $data['amenity_item_ids'] : [$data['amenity_item_ids']];
            
            $amenityItems = $this->amenityItemService->getItemsForAmenity($data['amenity_id'])
                ->whereIn('id', $amenityItemIds)
                ->where('status', 'active');

            if ($amenityItems->count() !== count($amenityItemIds)) {
                return [
                    'status' => false,
                    'message' => 'Invalid or inactive items selected',
                    'data' => []
                ];
            }

            // Calculate amounts and prepare booking items
            $totalAmount = 0;
            $payableAmount = 0;
            $totalDuration = 0;
            $bookingItems = [];

            foreach ($amenityItems as $item) {
                // Check if item is included in user's package
                $isIncluded = false;
                if ($userPackage) {
                    $isIncluded = \App\Models\PackageAmenityItem::where('package_id', $userPackage->package_id)
                        ->where('amenity_item_id', $item->id)
                        ->exists();
                }

                $itemPrice = $isIncluded ? 0 : $item->price;
                $totalAmount += $item->price; // Total includes all items
                $payableAmount += $itemPrice; // Payable only for non-included items
                $totalDuration += $item->duration_minutes;

                $bookingItems[] = [
                    'amenity_item_id' => $item->id,
                    'item_title' => $item->title,
                    'duration_minutes' => $item->duration_minutes,
                    'was_included_in_package' => $isIncluded,
                    'price' => $itemPrice,
                ];
            }

            // Calculate end time
            $startTime = Carbon::parse($data['booking_date'] . ' ' . $data['booking_time']);
            $endTime = $startTime->copy()->addMinutes($totalDuration);

            // Prepare booking data
            $bookingData = [
                'user_id' => $userId,
                'amenity_id' => $data['amenity_id'],
                'package_id' => $userPackage?->package_id,
                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'],
                'end_time' => $endTime->format('H:i:s'),
                'status' => 'pending',
                'booking_amount' => $totalAmount,
                'total_amount' => $totalAmount,
                'payable_amount' => $payableAmount,
                'payment_status' => $payableAmount > 0 ? 'pending' : 'paid',
                'additional_note' => $data['additional_note'] ?? null,
            ];

            // Create booking with items using service
            $booking = $this->amenityBookingService->createBookingWithItems($bookingData, $bookingItems);

            // Clear cache
            $this->clearCache();

            return [
                'status' => true,
                'message' => 'Booking created successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'amenity' => [
                        'id' => $amenity->id,
                        'title' => $amenity->title,
                        'description' => $amenity->description,
                        'icon_url' => $amenity->icon_url,
                    ],
                    'booking_date' => $booking->booking_date->format('l, F d'),
                    'booking_time' => $booking->booking_time->format('H:i A'),
                    'end_time' => $booking->end_time->format('H:i A'),
                    'status' => $booking->status,
                    'total_amount' => $booking->total_amount,
                    'payable_amount' => $booking->payable_amount,
                    'payment_status' => $booking->payment_status,
                    'items' => $booking->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->item_title,
                            'duration_minutes' => $item->duration_minutes,
                            'duration_text' => $item->duration_text,
                            'was_included' => $item->was_included_in_package,
                            'price' => $item->price,
                        ];
                    }),
                    'reminder_message' => $this->getReminderMessage($amenity->id),
                ]
            ];
        });
    }

    /**
     * Get hardcoded date array (next 7 days)
     * TODO: Make this dynamic in future
     */
    private function getDateArray(): array
    {
        $dates = [];
        $today = Carbon::today();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            $dates[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('d'),
                'day_name' => $date->format('D'),
                'label' => $i === 0 ? 'Today' : ($i === 1 ? 'Tomorrow' : $date->format('M d')),
            ];
        }

        return $dates;
    }

    /**
     * Get hardcoded time array
     * TODO: Make this dynamic in future
     */
    private function getTimeArray(): array
    {
        return [
            ['value' => '09:00', 'label' => '9:00 AM'],
            ['value' => '10:00', 'label' => '10:00 AM'],
            ['value' => '11:00', 'label' => '11:00 AM'],
            ['value' => '12:00', 'label' => '12:00 PM'],
            ['value' => '13:00', 'label' => '1:00 PM'],
            ['value' => '14:00', 'label' => '2:00 PM'],
            ['value' => '15:00', 'label' => '3:00 PM'],
            ['value' => '16:00', 'label' => '4:00 PM'],
            ['value' => '17:00', 'label' => '5:00 PM'],
            ['value' => '18:00', 'label' => '6:00 PM'],
        ];
    }

    private function getBookingsEmptyMessageData(int $amenityId): array
    {
        switch ($amenityId) {
            case 1:
                return [
                    'title' => 'No Bookigs Yet',
                    'message' => 'No bookings found for this amenity',
                    'button_text' => 'Book Spa Treatment',
                ];
            case 2:
                return [
                    'title' => 'No Bookigs Yet',
                    'message' => 'No bookings found for this amenity',
                    'button_text' => 'Book Gym Session',
                ];
            case 3:
                return [
                    'title' => 'No Bookigs Yet',
                    'message' => 'No bookings found for this amenity',
                    'button_text' => 'Book Pool Session',
                ];
            default:
                return [
                    'title' => 'No Bookigs Yet',
                    'message' => 'No bookings found for this amenity',
                    'button_text' => 'Book Amenity',
                ];
        }
    }

    private function getReminderMessage(int $amenityId): string
    {
        switch ($amenityId) {
            case 1:
                return "Please be ready 15 minutes early. We'll send you a reminder one hour before your appointment.";
            case 2:
                return "Please be ready 15 minutes early. We'll send you a reminder one hour before your appointment.";
            case 3:
                return "Please be ready 15 minutes early. We'll send you a reminder one hour before your appointment.";
            default:
                return 'No reminder text...';
        }
    }
}