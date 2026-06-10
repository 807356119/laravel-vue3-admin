<?php

namespace App\Contracts;

interface RoleRepositoryInterface
{
    public function getAllRoles();

    public function findById(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function assignPermissions(int $roleId, array $permissions);

    public function getRoleWithPermissions(int $roleId);
}
