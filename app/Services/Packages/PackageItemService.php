<?php

namespace App\Services\Packages;

use App\Services\Core\BaseService;
use App\Models\PackageItem;
use App\Services\Categories\CategoryService;
use App\Services\CourseMaterials\CourseMaterialService;
use App\Services\Courses\CourseService;
use App\Services\CourseUnits\CourseUnitService;

class PackageItemService extends BaseService
{
    protected string $modelClass = PackageItem::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration for package items
     */
    public function getFilterConfig(): array
    {
        return [
            'item_type' => [
                'type' => 'exact',
                'label' => 'Item Type',
                'col' => 3,
                'options' => PackageItem::getItemTypeOptions(),
                'placeholder' => 'All Types'
            ],
            'status' => [
                'type' => 'exact',
                'label' => 'Status',
                'col' => 3,
                'options' => PackageItem::getStatusOptions(),
                'placeholder' => 'All Statuses'
            ],
            'package_id' => [
                'type' => 'exact',
                'label' => 'Package',
                'col' => 3,
                'options' => $this->getPackageOptions(),
                'placeholder' => 'All Packages'
            ]
        ];
    }

    /**
     * Get search configuration for UI
     */
    public function getSearchConfig(): array
    {
        return [
            'search_fields' => $this->getSearchFieldsConfig(),
            'default_search_fields' => $this->getDefaultSearchFields(),
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'item_type' => 'Item Type',
            'package.title' => 'Package Title',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['item_type', 'package.title'];
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
     * Get filtered data with search and filters
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->newQuery()->with(['package', 'course']);

        // Apply search
        if (!empty($params['search'])) {
            $this->applySearch($query, $params['search']);
        }

        // Apply filters
        if (!empty($params['filters'])) {
            $this->applyFilters($query, $params['filters']);
        }

        // Apply sorting
        $this->applySorting($query, $params['sort_by'] ?? null, $params['sort_dir'] ?? 'asc');

        // Apply pagination if requested
        if (isset($params['paginate']) && $params['paginate']) {
            return $query->paginate($params['per_page'] ?? 15);
        }

        return $query->get();
    }

    /**
     * Get package options for filter dropdown
     */
    public function getPackageOptions(): array
    {
        return app(PackageService::class)->getPackageOptions();
    }

    /**
     * Get items for a specific package
     */
    public function getItemsForPackage(int $packageId)
    {
        return $this->model->where('package_id', $packageId)->sorted()->get();
    }

    /**
     * Create item for a package
     */
    public function createItemForPackage(int $packageId, array $data): PackageItem
    {
        $data['package_id'] = $packageId;
        return $this->store($data);
    }

    /**
     * Update sort order for package items
     */
    public function updateSortOrder(array $order): bool
    {
        foreach ($order as $position => $id) {
            $item = $this->model->find($id);
            if ($item) {
                $item->update(['sort_order' => $position + 1]);
            }
        }
        return true;
    }

    /**
     * Get available items for a specific type with better organization
     */
    public function getAvailableItems(string $itemType): array
    {
        switch ($itemType) {
            case 'category':
                return app(CategoryService::class)->getActiveCategories()
                    ->withCount('courses')
                    ->orderBy('name')
                    ->get()
                    ->mapWithKeys(function ($category) {
                        return [$category->id => $category->name . ' (' . $category->courses_count . ' courses)'];
                    })
                    ->toArray();
                    
            case 'course':
                return app(CourseService::class)->getPublishedCourses()
                    ->with(['category'])
                    ->withCount('units')
                    ->orderBy('title')
                    ->get()
                    ->mapWithKeys(function ($course) {
                        $categoryName = $course->category ? $course->category->name : 'No Category';
                        return [$course->id => $course->title . ' (' . $categoryName . ' - ' . $course->units_count . ' units)'];
                    })
                    ->toArray();
                    
            case 'course_unit':
                return app(CourseUnitService::class)->getActiveCourseUnits()
                    ->with('course')
                    ->orderBy('title')
                    ->get()
                    ->mapWithKeys(function ($unit) {
                        $courseName = $unit->course ? $unit->course->title : 'No Course';
                        return [$unit->id => $unit->title . ' (' . $courseName . ')'];
                    })
                    ->toArray();
                    
            case 'course_material':
                return app(CourseMaterialService::class)->getPublishedMaterials()
                    ->whereIn('type', ['video', 'document', 'scorm'])
                    ->with('course')
                    ->orderBy('title')
                    ->get()
                    ->mapWithKeys(function ($material) {
                        $courseName = $material->course ? $material->course->title : 'No Course';
                        return [$material->id => $material->title . ' (' . $material->type . ' - ' . $courseName . ')'];
                    })
                    ->toArray();
                    
            default:
                return [];
        }
    }

}