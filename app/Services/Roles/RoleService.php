<?php

namespace App\Services\Roles;

use App\Models\Role;
use App\Services\Core\BaseService;
use App\Http\Resources\Roles\AppRoleCollection;
use App\Http\Resources\Roles\AppRoleResource;

class RoleService extends BaseService
{
    protected string $modelClass = Role::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filter configuration
     */
    public function getFilterConfig(): array
    {
        return [
            
        ];
    }

    /**
     * Get search fields configuration for UI
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
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'sort_order', 'direction' => 'asc'];
    }

    /**
     * Override store to set sort_order
     */
    public function store(array $data): Role
    {
        $maxSortOrder = $this->model->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;

        return parent::store($data);
    }

    // Role-specific methods (keep only what's unique to roles)
    public function getActiveRoles()
    {
        return $this->model->where('status', 1)->sorted()->get();
    }

    public function getSystemRoles()
    {
        return $this->model->whereIn('id', [
            Role::ADMIN,
            Role::NURSE,
            Role::CLIENT
        ])->sorted()->get();
    }

    public function getCustomRoles()
    {
        return $this->model->whereNotIn('id', [
            Role::ADMIN,
            Role::NURSE,
            Role::CLIENT
        ])->sorted()->get();
    }

    public function getAppRoles(): array
    {
        $roles = $this->model->where('status', 1)->sorted()->get();
        return (new AppRoleCollection($roles))->toArray(request());
    }

    public function getAppSystemRoles(): array
    {
        $roles = $this->model->whereIn('id', [
            Role::ADMIN,
            Role::NURSE,
            Role::CLIENT
        ])->where('status', 1)->sorted()->get();
        return (new AppRoleCollection($roles))->toArray(request());
    }

    public function getAppCustomRoles(): array
    {
        $roles = $this->model->whereNotIn('id', [
            Role::ADMIN,
            Role::NURSE,
            Role::CLIENT
        ])->where('status', 1)->sorted()->get();
        return (new AppRoleCollection($roles))->toArray(request());
    }

    public function getAppRole(int $id): array
    {
        $role = $this->model->where('status', 1)->find($id);
        
        if (!$role) {
            return [];
        }
        
        return (new AppRoleResource($role))->toArray(request());
    }

    // Permission management (unique to roles)
    public function assignPermissions(int $roleId, array $permissionIds): bool
    {
        $role = $this->model->find($roleId);
        if (!$role) {
            return false;
        }

        return $role->permissions()->sync($permissionIds);
    }

    public function removePermissions(int $roleId, array $permissionIds): bool
    {
        $role = $this->model->find($roleId);
        if (!$role) {
            return false;
        }

        return $role->permissions()->detach($permissionIds);
    }

    public function getRolePermissions(int $roleId)
    {
        $role = $this->model->find($roleId);
        if (!$role) {
            return collect();
        }

        return $role->permissions;
    }

    public function hasPermission(int $roleId, string $permission): bool
    {
        $role = $this->model->find($roleId);
        if (!$role) {
            return false;
        }

        return $role->permissions()->where('name', $permission)->exists();
    }

    /**
     * Check if a role is a system role (cannot be edited or deleted)
     */
    public function isSystemRole(int $roleId): bool
    {
        return in_array($roleId, [Role::ADMIN, Role::STUDENT, Role::TUTOR]);
    }

    /**
     * Override update to prevent editing system roles
     */
    public function update(int $id, array $data): Role
    {
        if ($this->isSystemRole($id)) {
            throw new \Exception('System roles cannot be modified');
        }

        return parent::update($id, $data);
    }

    /**
     * Override delete to prevent deleting system roles
     */
    public function delete(int $id): bool
    {
        if ($this->isSystemRole($id)) {
            throw new \Exception('System roles cannot be deleted');
        }

        return parent::delete($id);
    }

    /**
     * Override bulkDelete to prevent deleting system roles
     */
    public function bulkDelete(array $ids): int
    {
        $systemRoleIds = array_intersect($ids, [Role::ADMIN, Role::STUDENT, Role::TUTOR]);
        
        if (!empty($systemRoleIds)) {
            throw new \Exception('System roles cannot be deleted');
        }

        return parent::bulkDelete($ids);
    }
}
