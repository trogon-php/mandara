<?php

namespace App\Services\Amenities;

use App\Models\Amenity;
use App\Services\Core\BaseService;
use Illuminate\Support\Facades\DB;

class AmenityService extends BaseService
{
    protected string $modelClass = Amenity::class;

    public function __construct()
    {
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
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['title', 'description'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Get active amenities with items
     */
    public function getActiveAmenitiesWithItems()
    {
        return $this->model->active()
            ->with(['activeItems' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->sorted()
            ->get();
    }

    public function getAmenitiesPaginated($perPage = 10, ?array $relations = [])
    {
        return $this->model->active()
            ->with($relations)
            ->sorted()
            ->paginate($perPage);
    }

    /**
     * Get amenity options for dropdowns
     */
    public function getAmenityOptions(): array
    {
        return $this->model->active()
            ->pluck('title', 'id')
            ->toArray();
    }
    public function store(array $data): Amenity
    {
    
        return DB::transaction(function () use ($data) {

            //  Extract repeater items
            $items = $data['options'] ?? [];
            unset($data['options']);

            //  Save main amenity (file uploads handled by BaseService)
            $amenity = parent::store($data);

            //  Save amenity items
            foreach ($items as $item) {
                $amenity->items()->create([
                    'title'            => $item['title'],
                    'description'      => $item['description'] ?? null,
                    'duration_minutes' => $item['duration_minutes'] ?? null,
                    'price'            => $item['price'] ?? null,
                    'status'           => $item['status'],
                ]);
            }

            return $amenity;
        });
    }
    public function update(int $id, array $data): ?Amenity
    {
        return DB::transaction(function () use ($id, $data) {

            //  Extract repeater items
            $items = $data['options'] ?? [];
            unset($data['options']);

            //  Update main amenity
            $amenity = parent::update($id, $data);
            if (!$amenity) {
                return null;
            }

            //  Reset amenity items (safe + simple)
            $amenity->items()->delete();

            foreach ($items as $item) {
                $amenity->items()->create([
                    'title'            => $item['title'],
                    'description'      => $item['description'] ?? null,
                    'duration_minutes' => $item['duration_minutes'] ?? null,
                    'price'            => $item['price'] ?? null,
                    'status'           => $item['status'],
                ]);
            }

            return $amenity;
        });
    }
    // Override find method to return amenity with items
    public function findForEdit(int $id): ?Amenity
    {
        $amenity = $this->model->with('items')->find($id);

        if (!$amenity) {
            return null;
        }

        $amenity->items = $amenity->items->map(fn ($item) => [
            'title'            => $item->title,
            'description'      => $item->description,
            'duration_minutes' => $item->duration_minutes,
            'duration_text'    => $item->duration_text,
            'price'            => $item->price,
            'status'           => $item->status,
        ])->toArray();

        return $amenity;
    }

}