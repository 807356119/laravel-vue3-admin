<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

/**
 * @group 用户管理
 *
 * 用户相关的接口
 */
class UserController extends Controller
{
    use ApiResponse;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 获取用户列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @queryParam page integer 页码. Example: 1
     * @queryParam size integer 每页数量. Example: 10
     * @queryParam keyword string 搜索关键词. Example: admin
     * @queryParam status string 用户状态 active,inactive. Example: active
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'keyword' => $request->get('keyword'),
                'status' => $request->get('status'),
            ];

            $users = $this->userService->getUserList(
                $filters,
                $request->get('size', 10)
            );

            return $this->paginated(
                UserResource::collection($users),
                '获取用户列表成功'
            );
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    /**
     * 创建用户
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam name string required 姓名. Example: 张三
     * @bodyParam email string required 邮箱. Example: zhangsan@example.com
     * @bodyParam password string required 密码. Example: 123456
     * @bodyParam role_id integer required 角色ID. Example: 1
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return $this->created(
                new UserResource($user),
                '用户创建成功'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 获取用户详情
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @urlParam id integer required 用户ID. Example: 1
     */
    public function show(int $id)
    {
        try {
            $user = $this->userService->getUserById($id);

            return $this->success(
                new UserResource($user),
                '获取用户详情成功'
            );
        } catch (\Exception $e) {
            return $this->notFound($e->getMessage());
        }
    }

    /**
     * 更新用户
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @urlParam id integer required 用户ID. Example: 1
     * @bodyParam name string 姓名. Example: 李四
     * @bodyParam email string 邮箱. Example: lisi@example.com
     * @bodyParam role_id integer 角色ID. Example: 2
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());

            return $this->updated(
                new UserResource($user),
                '用户更新成功'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 删除用户
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @urlParam id integer required 用户ID. Example: 1
     */
    public function destroy(int $id)
    {
        try {
            $this->userService->deleteUser($id);

            return $this->deleted('用户删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 切换用户状态
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @urlParam id integer required 用户ID. Example: 1
     */
    public function toggleStatus(int $id)
    {
        try {
            $user = $this->userService->toggleUserStatus($id);

            return $this->updated(
                new UserResource($user),
                '状态更新成功'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 重置密码
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @urlParam id integer required 用户ID. Example: 1
     * @bodyParam password string required 新密码. Example: newpassword123
     */
    public function resetPassword(Request $request, int $id)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:32'],
        ]);

        try {
            $this->userService->resetPassword($id, $request->password);

            return $this->success(null, '密码重置成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
