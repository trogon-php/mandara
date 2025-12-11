<?php

namespace App\Services\Integrations;

use App\Models\ClientCredential;
use App\Services\Core\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ClientCredentialService extends BaseService
{
    protected string $modelClass = ClientCredential::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration for the admin interface
     */
    public function getFilterConfig(): array
    {
        return [
            
        ];
    }

    /**
     * Get search fields configuration
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'credential_key' => 'Credential Key',
            'provider' => 'Provider',
        ];
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'credential_key', 'provider'];
    }

    /**
     * Get default sorting configuration
     */
    public function getDefaultSorting(): array
    {
        return [
            'field' => 'id',
            'direction' => 'asc'
        ];
    }

    /**
     * Override getFilteredData to handle custom filters
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->newQuery();

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
     * Override applyFilters to handle custom status filter
     */
    protected function applyFilters($query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') continue;

            switch ($key) {
                case 'provider':
                    $query->where('provider', $value);
                    break;
                case 'status':
                    if ($value === 'complete') {
                        $query->whereNotNull('account_key')
                              ->whereNotNull('account_secret');
                    } elseif ($value === 'incomplete') {
                        $query->where(function($q) {
                            $q->whereNull('account_key')
                              ->orWhereNull('account_secret');
                        });
                    }
                    break;
                case 'date_from':
                    $query->whereDate('created_at', '>=', $value);
                    break;
                case 'date_to':
                    $query->whereDate('created_at', '<=', $value);
                    break;
            }
        }
    }

    /**
     * Store a new client credential with encryption
     */
    public function store(array $data): Model
    {
        return parent::store($data);
    }

    /**
     * Update client credential with encryption
     */
    public function update(int $id, array $data): ?Model
    {
        return parent::update($id, $data);
    }

    /**
     * Get credentials by provider
     */
    public function getByProvider(string $provider): Collection
    {
        return $this->model->byProvider($provider)->get();
    }

    /**
     * Get credentials by credential key
     */
    public function getByCredentialKey(string $credentialKey): ?Model
    {
        return $this->model->byCredentialKey($credentialKey)->first();
    }

    /**
     * Get complete credentials (with both key and secret)
     */
    public function getCompleteCredentials(): Collection
    {
        return $this->model->whereNotNull('account_key')
                           ->whereNotNull('account_secret')
                           ->get();
    }

    /**
     * Get incomplete credentials
     */
    public function getIncompleteCredentials(): Collection
    {
        return $this->model->where(function($query) {
            $query->whereNull('account_key')
                  ->orWhereNull('account_secret');
        })->get();
    }


}
