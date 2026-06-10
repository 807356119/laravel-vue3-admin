<?php

namespace App\Services;

use App\Contracts\AccountRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AccountService
{
    protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * 获取用户的账户列表
     */
    public function getUserAccountList(int $userId, array $filters, int $perPage = 15)
    {
        try {
            return $this->accountRepository->getUserAccounts($userId, $filters, $perPage);
        } catch (\Exception $e) {
            Log::error('获取账户列表失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 获取账户详情
     */
    public function getAccountById(int $id)
    {
        try {
            return $this->accountRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('获取账户详情失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 创建账户
     */
    public function createAccount(array $data)
    {
        DB::beginTransaction();

        try {
            $account = $this->accountRepository->create($data);

            DB::commit();

            Log::info('账户创建成功', ['account_id' => $account->id]);

            return $account;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('创建账户失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 更新账户
     */
    public function updateAccount(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $account = $this->accountRepository->update($id, $data);

            DB::commit();

            Log::info('账户更新成功', ['account_id' => $id]);

            return $account;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('更新账户失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 删除账户
     */
    public function deleteAccount(int $id)
    {
        DB::beginTransaction();

        try {
            // 获取账户信息
            $account = $this->accountRepository->findById($id);

            // 删除关联的图片
            if ($account->images) {
                foreach ($account->images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            $this->accountRepository->delete($id);

            DB::commit();

            Log::info('账户删除成功', ['account_id' => $id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('删除账户失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 切换收藏状态
     */
    public function toggleFavorite(int $id)
    {
        DB::beginTransaction();

        try {
            $account = $this->accountRepository->toggleFavorite($id);

            DB::commit();

            Log::info('账户收藏状态切换', ['account_id' => $id, 'is_favorite' => $account->is_favorite]);

            return $account;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('切换收藏状态失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 获取解密后的密码
     */
    public function getDecryptedPassword(int $id)
    {
        try {
            return $this->accountRepository->getDecryptedPassword($id);
        } catch (\Exception $e) {
            Log::error('获取密码失败: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 上传图片
     */
    public function uploadImage($file)
    {
        try {
            $path = $file->store('accounts', 'public');
            return $path;
        } catch (\Exception $e) {
            Log::error('上传图片失败: ' . $e->getMessage());
            throw $e;
        }
    }
}
