<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Core\BaseService;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;

class AdminService extends BaseService
{
    protected string $modelClass = User::class;
    protected int $roleId = Role::ADMIN->value;

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
                    '1' => 'Active',
                    '0' => 'Inactive',
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
}
