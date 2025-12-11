<?php

namespace App\Services\Packages;

use App\Services\Core\BaseService;
use App\Models\PackageFeature;

class PackageFeatureService extends BaseService
{
    protected $modelClass = PackageFeature::class;

    /**
     * Get filter configuration for package features
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => PackageFeature::getStatusOptions(),
                'placeholder' => 'All Statuses'
            ],
            'package_id' => [
                'type' => 'select',
                'label' => 'Package',
                'options' => $this->getPackageOptions(),
                'placeholder' => 'All Packages'
            ]
        ];
    }

    /**
     * Get search configuration for package features
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
            'title' => 'Title',
            'description' => 'Description',
            'package.title' => 'Package Title',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'description', 'package.title'];
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
        $query = $this->model->newQuery()->with(['package']);

        // Apply search
        if (!empty($params['search'])) {
            $searchFields = $this->getDefaultSearchFields();
            $query->where(function ($q) use ($params, $searchFields) {
                foreach ($searchFields as $field) {
                    if (strpos($field, '.') !== false) {
                        // Handle relationship columns
                        $parts = explode('.', $field);
                        $q->orWhereHas($parts[0], function ($subQ) use ($parts, $params) {
                            $subQ->where($parts[1], 'like', '%' . $params['search'] . '%');
                        });
                    } else {
                        $q->orWhere($field, 'like', '%' . $params['search'] . '%');
                    }
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
                    case 'package_id':
                        $query->where('package_id', $value);
                        break;
                }
            }
        }

        return $query->sorted()->paginate(10);
    }

    /**
     * Get package options for dropdown/select
     */
    public function getPackageOptions(): array
    {
        return \App\Models\Package::active()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * Get features for a specific package
     */
    public function getFeaturesForPackage(int $packageId)
    {
        return $this->model->where('package_id', $packageId)
            ->sorted()
            ->get();
    }

    /**
     * Create feature for a package
     */
    public function createFeatureForPackage(int $packageId, array $data): PackageFeature
    {
        $data['package_id'] = $packageId;
        return $this->store($data);
    }

    /**
     * Get package feature statistics
     */
    public function getFeatureStats(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'inactive' => $this->model->where('status', 'inactive')->count(),
        ];
    }
}

