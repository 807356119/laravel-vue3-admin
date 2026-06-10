<?php

namespace App\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Models\Account;

class AccountRepository implements AccountRepositoryInterface
{
    protected $model;

    public function __construct(Account $account)
    {
        $this->model = $account;
    }

    public function getUserAccounts(int $userId, array $filters, int $perPage = 15)
    {
        $query = $this->model->where('user_id', $userId);

        // 搜索关键词
        if (isset($filters['keyword']) && !empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('account_name', 'like', "%{$keyword}%")
                  ->orWhere('account_number', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // 类型过滤
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // 分类过滤
        if (isset($filters['category']) && !empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // 收藏过滤
        if (isset($filters['is_favorite']) && $filters['is_favorite']) {
            $query->where('is_favorite', true);
        }

        return $query->orderBy('is_favorite', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $account = $this->findById($id);

        // 如果没有传password，不更新密码
        if (!isset($data['password']) || empty($data['password'])) {
            unset($data['password']);
        }

        $account->update($data);

        return $account->fresh();
    }

    public function delete(int $id)
    {
        $account = $this->findById($id);
        $account->delete();

        return true;
    }

    public function toggleFavorite(int $id)
    {
        $account = $this->findById($id);
        $account->is_favorite = !$account->is_favorite;
        $account->save();

        return $account;
    }

    public function getDecryptedPassword(int $id)
    {
        $account = $this->findById($id);
        return $account->decrypted_password;
    }
}
