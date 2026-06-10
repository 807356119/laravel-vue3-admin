<?php

namespace App\Services;

use App\Contracts\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * 获取所有角色
     */
    public function getAllRoles()
    {
        try {
            return $this->roleRepository->getAllRoles();
        } catch (\Exception $e) {
            Log::error('获取角色列表失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 获取单个角色
     */
    public function getRoleById(int $id)
    {
        try {
            return $this->roleRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('获取角色失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 创建角色
     */
    public function createRole(array $data)
    {
        DB::beginTransaction();

        try {
            $role = $this->roleRepository->create($data);

            DB::commit();

            Log::info('角色创建成功', ['role_id' => $role->id]);

            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('创建角色失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 更新角色
     */
    public function updateRole(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $role = $this->roleRepository->update($id, $data);

            DB::commit();

            Log::info('角色更新成功', ['role_id' => $id]);

            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('更新角色失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 删除角色
     */
    public function deleteRole(int $id)
    {
        DB::beginTransaction();

        try {
            $this->roleRepository->delete($id);

            DB::commit();

            Log::info('角色删除成功', ['role_id' => $id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('删除角色失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 分配权限
     */
    public function assignPermissions(int $roleId, array $permissions)
    {
        DB::beginTransaction();

        try {
            $role = $this->roleRepository->assignPermissions($roleId, $permissions);

            DB::commit();

            Log::info('权限分配成功', ['role_id' => $roleId, 'permissions_count' => count($permissions)]);

            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('分配权限失败: ' . $e->getMessage());
            throw $e;
        }
    }
}
