<?php

namespace App\Services\Packages;

use App\Services\Core\BaseService;
use App\Models\Package;

class PackageService extends BaseService
{
    protected $modelClass = Package::class;

    public function getModel(): Package
    {
        return $this->model;
    }
    /**
     * Get filter configuration for packages
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => Package::getStatusOptions(),
                'placeholder' => 'All Statuses'
            ],
            'has_offer' => [
                'type' => 'select',
                'label' => 'Has Offer',
                'options' => [
                    '1' => 'Yes',
                    '0' => 'No'
                ],
                'placeholder' => 'All Packages'
            ],
            'system_generated' => [
                'type' => 'select',
                'label' => 'System Generated',
                'options' => [
                    '1' => 'Yes',
                    '0' => 'No'
                ],
                'placeholder' => 'All Packages'
            ],
        ];
    }

    /**
     * Get search configuration for packages
     */
    public function getSearchConfig(): array
    {
        return [
            'search_fields' => $this->getSearchFieldsConfig(),
            'default_search_fields' => $this->getDefaultSearchFields(),
        ];
    }

    /**
     * Get filtered data with search and filters
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->newQuery();
        // Apply search
        if (!empty($params['search'])) {
            $searchFields = $this->getDefaultSearchFields();
            $query->where(function ($q) use ($params, $searchFields) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'like', '%' . $params['search'] . '%');
                }
            });
        }

        // Apply filters
        if (!empty($params['filters'])) {
            foreach ($params['filters'] as $filter => $value) {
                switch ($filter) {
                    case 'status':
                        $query->where('status', $value);
                        break;
                    case 'has_offer':
                        if ($value == '1') {
                            $query->whereNotNull('offer_price');
                        } elseif ($value == '0') {
                            $query->whereNull('offer_price');
                        }
                        break;
                    case 'system_generated':
                        if ($value == '1') {
                            $query->where('system_generated', 1);
                        } elseif ($value == '0') {
                            $query->where('system_generated', 0);
                        }
                        break;
                }
            }
        }

        return $query->sorted()->paginate(10);
    }

    /**
     * Get packages for dropdown/select options
     */
    public function getPackageOptions(): array
    {
        return $this->model->active()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * Get active packages with offer
     */
    public function getActivePackagesWithOffer()
    {
        return $this->model->active()
            ->withOffer()
            ->sorted()
            ->get();
    }

    /**
     * Get package statistics
     */
    public function getPackageStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->where('status', 'inactive')->count(),
            'with_offer' => $this->model->withOffer()->count(),
            'expired' => $this->model->where('expire_date', '<', now())->count(),
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'description'];
    }

    /**
     * Get default sorting configuration
     */
    public function getDefaultSorting(): array
    {
        return [
            'field' => 'sort_order',
            'direction' => 'asc'
        ];
    }

    /**
     * Get packages that contain a specific course
     */
    public function getPackagesByCourseId(int $courseId): array
    {
        $packages = $this->model->active()
            ->whereHas('items', function ($query) use ($courseId) {
                $query->where('item_type', 'course')
                      ->where('item_id', $courseId)
                      ->where('status', 'active');
            })
            ->with(['items' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }, 'features' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }])
            ->sorted()
            ->get();

        return $packages->map(function ($package, $index) {
            return (new \App\Http\Resources\Packages\AppPackageResource($package, $index === 0))->toArray(request());
        })->toArray();
    }

    /**
     * Get active packages for API
     */
    public function getActivePackagesForApi(): array
    {
        $packages = $this->model->active()
            ->with(['items' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }, 'features' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }])
            ->sorted()
            ->get();

        return $packages->map(function ($package, $index) {
            return (new \App\Http\Resources\Packages\AppPackageResource($package, $index === 0))->toArray(request());
        })->toArray();
    }

    /**
     * Get package details for API
     */
    public function getPackageDetailsForApi(int $id): array
    {
        $package = $this->model->active()
            ->where('id', $id)
            ->with(['items' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }, 'features' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }])
            ->first();

        if (!$package) {
            return [];
        }

        return (new \App\Http\Resources\Packages\AppPackageResource($package, true))->toArray(request());
    }

    /**
     * Get packages by course unit ID
     */
    public function getPackagesByUnitId(int $unitId, bool $includeParent = false): array
    {
        $query = $this->model->active()
            ->whereHas('items', function ($q) use ($unitId) {
                $q->where('item_type', 'course_unit')
                  ->where('item_id', $unitId)
                  ->where('status', 'active');
            });

        // If includeParent is true, also get packages that contain the parent course
        if ($includeParent) {
            $query->orWhereHas('items', function ($q) use ($unitId) {
                $q->where('item_type', 'course')
                  ->whereIn('item_id', function ($subQuery) use ($unitId) {
                      $subQuery->select('course_id')
                               ->from('course_units')
                               ->where('id', $unitId);
                  })
                  ->where('status', 'active');
            });
        }

        $packages = $query->with(['items' => function ($query) {
            $query->where('status', 'active')->orderBy('sort_order');
        }, 'features' => function ($query) {
            $query->where('status', 'active')->orderBy('sort_order');
        }])
        ->sorted()
        ->get();

        return $packages->map(function ($package, $index) {
            return (new \App\Http\Resources\Packages\AppPackageResource($package, $index === 0))->toArray(request());
        })->toArray();
    }

    /**
     * Get packages by course material ID
     */
    public function getPackagesByMaterialId(int $materialId, bool $includeParent = false): array
    {
        $query = $this->model->active()
            ->whereHas('items', function ($q) use ($materialId) {
                $q->where('item_type', 'course_material')
                  ->where('item_id', $materialId)
                  ->where('status', 'active');
            });

        // If includeParent is true, also get packages that contain the parent course/unit
        if ($includeParent) {
            $query->orWhereHas('items', function ($q) use ($materialId) {
                $q->where(function ($subQuery) use ($materialId) {
                    // Get packages containing the course
                    $subQuery->where('item_type', 'course')
                            ->whereIn('item_id', function ($courseQuery) use ($materialId) {
                                $courseQuery->select('course_id')
                                           ->from('course_materials')
                                           ->where('id', $materialId);
                            });
                })
                ->orWhere(function ($subQuery) use ($materialId) {
                    // Get packages containing the course unit
                    $subQuery->where('item_type', 'course_unit')
                            ->whereIn('item_id', function ($unitQuery) use ($materialId) {
                                $unitQuery->select('unit_id')
                                        ->from('course_materials')
                                        ->where('id', $materialId);
                            });
                });
            })
            ->where('status', 'active');
        }

        $packages = $query->with(['items' => function ($query) {
            $query->where('status', 'active')->orderBy('sort_order');
        }, 'features' => function ($query) {
            $query->where('status', 'active')->orderBy('sort_order');
        }])
        ->sorted()
        ->get();

        return $packages->map(function ($package, $index) {
            return (new \App\Http\Resources\Packages\AppPackageResource($package, $index === 0))->toArray(request());
        })->toArray();
    }

    /**
     * Get packages by category ID
     */
    public function getPackagesByCategoryId(int $categoryId): array
    {
        $packages = $this->model->active()
            ->whereHas('items', function ($query) use ($categoryId) {
                $query->where('item_type', 'category')
                      ->where('item_id', $categoryId)
                      ->where('status', 'active');
            })
            ->with(['items' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }, 'features' => function ($query) {
                $query->where('status', 'active')->orderBy('sort_order');
            }])
            ->sorted()
            ->get();

        return $packages->map(function ($package, $index) {
            return (new \App\Http\Resources\Packages\AppPackageResource($package, $index === 0))->toArray(request());
        })->toArray();
    }

    /**
     * Get packages with filters and search
     */
    public function getPackagesWithFilters(array $params = []): array
    {
        $query = $this->model->active();

        // Apply search
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('title', 'like', '%' . $params['search'] . '%')
                  ->orWhere('description', 'like', '%' . $params['search'] . '%');
            });
        }

        // Apply filters
        if (!empty($params['has_offer'])) {
            if ($params['has_offer'] == '1') {
                $query->whereNotNull('offer_price');
            } elseif ($params['has_offer'] == '0') {
                $query->whereNull('offer_price');
            }
        }

        if (!empty($params['min_price'])) {
            $query->where('price', '>=', $params['min_price']);
        }

        if (!empty($params['max_price'])) {
            $query->where('price', '<=', $params['max_price']);
        }

        if (!empty($params['duration_days'])) {
            $query->where('duration_days', '<=', $params['duration_days']);
        }

        // Apply sorting
        $sortField = $params['sort_field'] ?? 'sort_order';
        $sortDirection = $params['sort_direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        $packages = $query->with(['items' => function ($query) {
            $query->where('status', 'active')->orderBy('sort_order');
        }, 'features' => function ($query) {
            $query->where('status', 'active')->orderBy('sort_order');
        }])
        ->get();

        return $packages->map(function ($package, $index) {
            return (new \App\Http\Resources\Packages\AppPackageResource($package, $index === 0))->toArray(request());
        })->toArray();
    }

    /**
     * Get packages by multiple criteria
     */
    public function getPackagesByCriteria(array $criteria): array
    {
        $query = $this->model->active();

        if (!empty($criteria['course_ids'])) {
            $query->whereHas('items', function ($q) use ($criteria) {
                $q->where('item_type', 'course')
                  ->whereIn('item_id', $criteria['course_ids'])
                  ->where('status', 'active');
            });
        }

        if (!empty($criteria['unit_ids'])) {
            $query->orWhereHas('items', function ($q) use ($criteria) {
                $q->where('item_type', 'course_unit')
                  ->whereIn('item_id', $criteria['unit_ids'])
                  ->where('status', 'active');
            });
        }

        if (!empty($criteria['material_ids'])) {
            $query->orWhereHas('items', function ($q) use ($criteria) {
                $q->where('item_type', 'course_material')
                  ->whereIn('item_id', $criteria['material_ids'])
                  ->where('status', 'active');
            });
        }

        if (!empty($criteria['category_ids'])) {
            $query->orWhereHas('items', function ($q) use ($criteria) {
                $q->where('item_type', 'category')
                  ->whereIn('item_id', $criteria['category_ids'])
                  ->where('status', 'active');
            });
        }

        $packages = $query->with(['items' => function ($query) {
            $query->where('status', 'active')->orderBy('sort_order');
        }, 'features' => function ($query) {
            $query->where('status', 'active')->orderBy('sort_order');
        }])
        ->sorted()
        ->get();

        return $packages->map(function ($package, $index) {
            return (new \App\Http\Resources\Packages\AppPackageResource($package, $index === 0))->toArray(request());
        })->toArray();
    }

    /**
     * Get active packages for selection
     */
    public function getActivePackages(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->active()->get();
    }
}
