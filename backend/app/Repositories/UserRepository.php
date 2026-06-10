<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function getAllUsers(array $filters, int $perPage = 10)
    {
        $query = $this->model->with('roles');

        // 搜索过滤
        if (isset($filters['keyword']) && !empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 状态过滤
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')
                     ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->model->with('roles')->findOrFail($id);
    }

    public function create(array $data)
    {
        // 加密密码
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->model->create($data);

        // 分配角色
        if (isset($data['role_id'])) {
            $user->roles()->attach($data['role_id']);
        }

        return $user->load('roles');
    }

    public function update(int $id, array $data)
    {
        $user = $this->findById($id);

        // 如果有密码，加密
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        // 更新角色
        if (isset($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        }

        return $user->fresh(['roles']);
    }

    public function delete(int $id)
    {
        $user = $this->findById($id);

        // 不能删除自己
        if ($user->id === auth()->id()) {
            throw new \Exception('不能删除自己');
        }

        $user->delete();

        return true;
    }

    public function toggleStatus(int $id)
    {
        $user = $this->findById($id);

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return $user;
    }

    public function resetPassword(int $id, string $password)
    {
        $user = $this->findById($id);

        $user->password = Hash::make($password);
        $user->save();

        return $user;
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }
}
