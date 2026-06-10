<?php

namespace App\Repositories;

use App\Contracts\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    protected $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function getAllRoles()
    {
        return $this->model->withCount('users')
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function findById(int $id)
    {
        return $this->model->with('permissions')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $role = $this->findById($id);
        $role->update($data);

        return $role;
    }

    public function delete(int $id)
    {
        $role = $this->findById($id);

        // 检查是否有用户使用该角色
        if ($role->users()->count() > 0) {
            throw new \Exception('该角色下还有用户，无法删除');
        }

        $role->delete();

        return true;
    }

    public function assignPermissions(int $roleId, array $permissions)
    {
        $role = $this->findById($roleId);
        $role->permissions()->sync($permissions);

        return $role->load('permissions');
    }

    public function getRoleWithPermissions(int $roleId)
    {
        return $this->findById($roleId);
    }
}
