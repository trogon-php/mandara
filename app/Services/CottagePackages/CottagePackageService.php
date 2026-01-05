<?php

namespace App\Services\CottagePackages;

use App\Models\CottagePackage;
use App\Services\Core\BaseService;
use Exception;

class CottagePackageService extends BaseService
{
    protected string $modelClass = CottagePackage::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration - used for CRUD filters
     */
    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    '1' => 'Active',
                    '0' => 'Inactive',
                ],
            ],
        ];
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'provider' => 'Provider',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'provider'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    public function store(array $data): CottagePackage
    {
        $maxSortOrder = $this->model->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;

        return parent::store($data);
    }
    public function getOptions(): array
    {
        return CottagePackage::orderBy('title')
            ->pluck('title', 'id')
            ->toArray();
    }
    public function getOptionsWithDuration(): array
    {
        return $this->model
            ->where('status', 'active')
            ->get()
            ->mapWithKeys(function ($pkg) {
                return [
                    $pkg->id => [
                        'label'    => $pkg->title . ' (' . $pkg->duration_days . ' days)',
                        'duration' => $pkg->duration_days,
                    ]
                ];
            })
            ->toArray();
    }
    /**
     * Find a package or throw exception
     */
    public function findOrFail(int $id): CottagePackage
    {
        $package = $this->model->find($id);

        if (!$package) {
            throw new Exception('Cottage package not found.');
        }

        return $package;
    }

}