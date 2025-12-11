<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Core\BaseService;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TutorService extends BaseService
{
    protected string $modelClass = User::class;
    protected int $roleId = Role::TUTOR->value;

    public function __construct()
    {
        parent::__construct();
    }

    public function store(array $data): Model
    {
        $data['role_id'] = $this->roleId;
        return parent::store($data);
    }

    public function update(int $id, array $data): ?Model
    {
        $data['role_id'] = $this->roleId;
        return parent::update($id, $data);
    }

    public function getFilterConfig(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'col' => 3,
                'options' => [
                    'active' => 'Active',
                    'pending' => 'Pending',
                    'blocked' => 'Blocked',
                ],
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
     * Override getFilteredData to filter only tutors (role_id = 3)
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->where('role_id', $this->roleId)
            ->with(['courseTutors.course']);

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
            return $query->paginate($params['per_page'] ?? 15);
        }

        return $query->get();
    }

    /**
     * Override getAll to filter only tutors
     */
    public function getAll(): Collection
    {
        return $this->model->where('role_id', $this->roleId)->sorted()->get();
    }

    /**
     * Override find to ensure we only find tutors
     */
    public function find(int $id, array $relations = []): ?Model
    {
        return $this->model->where('role_id', $this->roleId)->with($relations)->find($id);
    }

    /**
     * Override delete to ensure we only delete tutors
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
     * Override bulkDelete to ensure we only delete tutors
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
}
