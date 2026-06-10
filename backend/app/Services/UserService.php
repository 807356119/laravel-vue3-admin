<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * 获取用户列表
     */
    public function getUserList(array $filters, int $perPage = 10)
    {
        try {
            return $this->userRepository->getAllUsers($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('获取用户列表失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 获取单个用户
     */
    public function getUserById(int $id)
    {
        try {
            return $this->userRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('获取用户失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 创建用户
     */
    public function createUser(array $data)
    {
        DB::beginTransaction();

        try {
            // 验证邮箱唯一性
            if ($this->userRepository->findByEmail($data['email'])) {
                throw new \Exception('邮箱已存在');
            }

            $user = $this->userRepository->create($data);

            DB::commit();

            Log::info('用户创建成功', ['user_id' => $user->id]);

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('创建用户失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 更新用户
     */
    public function updateUser(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            // 如果更新邮箱，验证唯一性
            if (isset($data['email'])) {
                $existingUser = $this->userRepository->findByEmail($data['email']);
                if ($existingUser && $existingUser->id !== $id) {
                    throw new \Exception('邮箱已被使用');
                }
            }

            $user = $this->userRepository->update($id, $data);

            DB::commit();

            Log::info('用户更新成功', ['user_id' => $id]);

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('更新用户失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 删除用户
     */
    public function deleteUser(int $id)
    {
        DB::beginTransaction();

        try {
            $this->userRepository->delete($id);

            DB::commit();

            Log::info('用户删除成功', ['user_id' => $id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('删除用户失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 切换用户状态
     */
    public function toggleUserStatus(int $id)
    {
        DB::beginTransaction();

        try {
            $user = $this->userRepository->toggleStatus($id);

            DB::commit();

            Log::info('用户状态切换成功', ['user_id' => $id, 'new_status' => $user->status]);

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('切换用户状态失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 重置密码
     */
    public function resetPassword(int $id, string $password)
    {
        DB::beginTransaction();

        try {
            // 密码强度验证
            if (strlen($password) < 6) {
                throw new \Exception('密码至少6位');
            }

            $user = $this->userRepository->resetPassword($id, $password);

            DB::commit();

            Log::info('密码重置成功', ['user_id' => $id]);

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('重置密码失败: ' . $e->getMessage());
            throw $e;
        }
    }
}
