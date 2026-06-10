<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\StoreAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Services\AccountService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

/**
 * @group 账户管理
 *
 * 管理银行卡、账号密码等敏感信息
 */
class AccountController extends Controller
{
    use ApiResponse;

    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * 获取账户列表
     */
    public function index(Request $request)
    {
        try {
            $userId = auth()->id();
            $filters = [
                'keyword' => $request->get('keyword'),
                'type' => $request->get('type'),
                'category' => $request->get('category'),
                'is_favorite' => $request->get('is_favorite'),
            ];

            $accounts = $this->accountService->getUserAccountList(
                $userId,
                $filters,
                $request->get('size', 15)
            );

            return $this->paginated(
                AccountResource::collection($accounts),
                '获取账户列表成功'
            );
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * 创建账户
     */
    public function store(StoreAccountRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $account = $this->accountService->createAccount($data);

            return $this->created(
                new AccountResource($account),
                '账户创建成功'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 获取账户详情
     */
    public function show(int $id)
    {
        try {
            $account = $this->accountService->getAccountById($id);

            // 验证权限
            if ($account->user_id !== auth()->id()) {
                return $this->forbidden('无权访问此账户');
            }

            return $this->success(
                new AccountResource($account),
                '获取账户详情成功'
            );
        } catch (\Exception $e) {
            return $this->notFound($e->getMessage());
        }
    }

    /**
     * 更新账户
     */
    public function update(UpdateAccountRequest $request, int $id)
    {
        try {
            $account = $this->accountService->getAccountById($id);

            // 验证权限
            if ($account->user_id !== auth()->id()) {
                return $this->forbidden('无权修改此账户');
            }

            $account = $this->accountService->updateAccount($id, $request->validated());

            return $this->updated(
                new AccountResource($account),
                '账户更新成功'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 删除账户
     */
    public function destroy(int $id)
    {
        try {
            $account = $this->accountService->getAccountById($id);

            // 验证权限
            if ($account->user_id !== auth()->id()) {
                return $this->forbidden('无权删除此账户');
            }

            $this->accountService->deleteAccount($id);

            return $this->deleted('账户删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 切换收藏状态
     */
    public function toggleFavorite(int $id)
    {
        try {
            $account = $this->accountService->getAccountById($id);

            // 验证权限
            if ($account->user_id !== auth()->id()) {
                return $this->forbidden('无权操作此账户');
            }

            $account = $this->accountService->toggleFavorite($id);

            return $this->updated(
                new AccountResource($account),
                '收藏状态更新成功'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 获取密码（解密）
     */
    public function getPassword(int $id)
    {
        try {
            $account = $this->accountService->getAccountById($id);

            // 验证权限
            if ($account->user_id !== auth()->id()) {
                return $this->forbidden('无权查看此密码');
            }

            $password = $this->accountService->getDecryptedPassword($id);

            return $this->success([
                'password' => $password
            ], '密码获取成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 上传图片
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
        ]);

        try {
            $path = $this->accountService->uploadImage($request->file('image'));

            return $this->success([
                'path' => $path,
                'url' => asset('storage/' . $path)
            ], '图片上传成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
