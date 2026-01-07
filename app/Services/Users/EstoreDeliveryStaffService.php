<?php

namespace App\Services\Users;

use App\Enums\Role;
use App\Models\User;
use App\Services\Core\BaseService;
use Illuminate\Database\Eloquent\Model;

class EstoreDeliveryStaffService extends BaseService
{
    protected string $modelClass = User::class;
    protected int $roleId = Role::ESTORE_DELIVERY_STAFF->value;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create student with role_id auto assigned
     */
    public function store(array $data): Model
    {
        $data['role_id'] = $this->roleId;
        return parent::store($data);
    }

    /**
     * Update student with role_id auto assigned
     */
    public function update(int $id, array $data): ?Model
    {
        $data['role_id'] = $this->roleId;
        return parent::update($id, $data);
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
                    'pending' => 'Pending',
                    'blocked' => 'Blocked',
                ],
            ],
            'date_range' => [
                'type' => 'date-range',
                'label' => 'Created Date',
                'col' => 4,
                'field' => 'created_at',
                'fromField' => 'date_from',
                'toField' => 'date_to',
                'placeholder' => 'Select date range...',
            ]
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['name', 'email', 'phone'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    /**
     * Override getFilteredData to filter only clients (role_id = 2)
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->where('role_id', $this->roleId);

        // Apply search
        if (!empty($params['search'])) {
            $this->applySearch($query, $params['search']);
        }

        // Apply filters
        if (!empty($params['filters'])) {
            $this->applyFilters($query, $params['filters']);
        }

        // Apply sorting
        $this->applySorting($query, $params['sort_by'] ?? null, $params['sort_dir'] ?? 'desc');

        // Apply pagination if requested
        if (isset($params['paginate']) && $params['paginate']) {
            return $query->paginate($params['per_page'] ?? 25);
        }

        return $query->get();
    }

    /**
     * Override getAll to filter only students
     */
    public function getAll(): \Illuminate\Support\Collection
    {
        return $this->model->where('role_id', $this->roleId)->sorted()->get();
    }

    /**
     * Override find to ensure we only find clients
     */
    public function find(int $id, array $relations = []): ?Model
    {
        return $this->model->where('role_id', $this->roleId)->with($relations)->find($id);
    }

    /**
     * Override delete to ensure we only delete clients
     */
    public function delete(int $id): bool
    {
        $record = $this->model->where('role_id', $this->roleId)->find($id);
        if (!$record) {
            return false;
        }

        $this->deleteAttachedFiles($record);
        return $record->delete();
    }

    /**
     * Override bulkDelete to ensure we only delete clients
     */
    public function bulkDelete(array $ids): int
    {
        $records = $this->model->where('role_id', $this->roleId)->whereIn('id', $ids)->get();

        foreach ($records as $record) {
            if ($record) {
                $this->deleteAttachedFiles($record);
            }
        }
        return $this->model->where('role_id', $this->roleId)->whereIn('id', $ids)->delete();
    }

    /**
     * Get clients options for dropdowns
     */
    public function getClientsOptions(): array
    {
        return $this->model->where('role_id', $this->roleId)
            ->sorted()
            ->get(['id', 'name', 'country_code', 'phone'])
            ->mapWithKeys(function ($student) {
                $displayName = $student->name;
                if ($student->country_code && $student->phone) {
                    $displayName .= ' [' . $student->country_code.' ' . $student->phone . ']';
                }
                return [$student->id => $displayName];
            })
            ->toArray();
    }

    /**
     * Override applyFilter to handle date_range filter properly
     */
    protected function applyFilter($query, string $field, $value, array $config)
    {
        if ($field === 'date_range' && $config['type'] === 'date_range') {
            $field = $config['field'] ?? 'created_at';
            if (isset($value['from'])) {
                $query->whereDate($field, '>=', $value['from']);
            }
            if (isset($value['to'])) {
                $query->whereDate($field, '<=', $value['to']);
            }
            return;
        }

        // Call parent method for other filters
        parent::applyFilter($query, $field, $value, $config);
    }
    /**
     * Get students for AJAX search with pagination
     */
    public function getSelect2AjaxOptions(array $params = [])
    {
        $search = $params['search'] ?? '';
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 15;
        
        $query = $this->model->where('role_id', $this->roleId)
            ->where('status', 'active');
        
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        return $query->select('id', 'name', 'phone', 'country_code')
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);
    }
    public function getJourneyStatus(int $userId): string
    {
        $user = $this->find($userId,['userMeta']);
        
        $isPreparing = $user->getMetaField('preparing_to_conceive');
        $isPregnant = $user->getMetaField('is_pregnant');
        $isDelivered = $user->getMetaField('is_delivered');

        if($isPreparing && $isPreparing == 1) {
            return 'preparing';
        }
        if($isPregnant && $isPregnant == 1) {
            return 'pregnant';
        }
        if($isDelivered && $isDelivered == 1) {
            return 'delivered';
        }
        return 'not determined';
    }
     /**
     * Find client by phone+country OR email (priority-based)
     */
    public function findExistingClient(
        ?string $phone,
        ?string $countryCode,
        ?string $email
    ): ?User {
        $query = User::query();

        // PRIMARY: phone + country code
        if (!empty($phone) && !empty($countryCode)) {
            $query->where('phone', $phone)
                  ->where('country_code', $countryCode);
        }
      
        // SECONDARY: email
        elseif (!empty($email)) {
            $query->where('email', $email);
        }
      
        return $query->first();
       
    }

    /**
     * Find existing client or create a new one
     */
    public function findOrCreate(array $data): User
    {
        //1. Try to find existing client (SAFE logic)
        $query = User::where('role_id', 2);

        if (!empty($data['phone']) && !empty($data['country_code'])) {
            $query->where('phone', $data['phone'])
                  ->where('country_code', $data['country_code']);
        } elseif (!empty($data['email'])) {
            $query->where('email', $data['email']);
        }

        $user = $query->first();

        if ($user) {
            return $user;
        }

        //2. Create new client (country_code WILL be saved)
        return User::create([
            'name'         => $data['name'],
            'email'        => $data['email'] ?? null,
            'phone'        => $data['phone'] ?? null,
            'country_code' => $data['country_code'] ?? null,
            'role_id'      => 2,
            'status'       => 'active',
            'password'     => bcrypt('password'), 
        ]);
    }
   

}
