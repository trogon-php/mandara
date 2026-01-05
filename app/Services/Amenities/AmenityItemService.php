<?php

namespace App\Services\Amenities;

use App\Models\AmenityItem;
use App\Services\Core\BaseService;
use App\Services\Amenities\AmenityService;
use Illuminate\Support\Facades\Log;

class AmenityItemService extends BaseService
{
    protected string $modelClass = AmenityItem::class;

    public function __construct(
        private AmenityService $amenityService
    ) {
        parent::__construct();
    }

    public function getFilterConfig(): array
    {
        return [
            'amenity_id' => [
                'type' => 'exact',
                'label' => 'Amenity',
                'col' => 3,
                'options' => $this->amenityService->getAmenityOptions(),
            ],
            'status' => [
                'type' => 'exact',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ],
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'amenity.title' => 'Amenity',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['title', 'description', 'amenity.title'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Get items for a specific amenity
     */
    public function getItemsForAmenity(int $amenityId)
    {
        return $this->model->where('amenity_id', $amenityId)
            ->active()
            ->sorted()
            ->get();
    }

    /**
     * Get items with package inclusion status for a user
     */
    public function getItemsWithPackageStatus(int $amenityId, ?int $userId = null)
    {
        $items = $this->getItemsForAmenity($amenityId);
        
        if (!$userId) {
            Log::info('No user ID provided');
            return $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'duration_minutes' => $item->duration_minutes,
                    'duration_text' => $item->duration_text,
                    'price' => $item->price,
                    'is_included' => false,
                ];
            });
        }

        // Get user's active package
        $userPackage = \App\Models\UserPackage::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            })
            ->first();
        Log::info('User package: ' . $userPackage);
        return $items->map(function ($item) use ($userPackage) {
            $isIncluded = false;
            if ($userPackage) {
                $isIncluded = \App\Models\PackageAmenityItem::where('package_id', $userPackage->package_id)
                    ->where('amenity_item_id', $item->id)
                    ->exists();
            }

            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'duration_minutes' => $item->duration_minutes,
                'duration_text' => $item->duration_text,
                'price' => $item->price,
                'is_included' => $isIncluded,
                'display_price' => $isIncluded ? 'Included' : '+' . number_format($item->price, 2),
            ];
        });
    }
}