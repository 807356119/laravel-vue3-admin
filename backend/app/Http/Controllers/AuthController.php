<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * 登录
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = auth()->attempt($credentials)) {
            return $this->error('邮箱或密码错误', 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * 注册
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * 获取当前用户信息（包含实时权限）
     */
    public function me()
    {
        $user = auth()->user();

        // 实时加载用户的角色和权限
        $user->load(['roles.permissions']);

        // 获取用户所有权限代码（使用正确的方法名）
        $permissions = $user->getPermissions();

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'roles' => $user->roles->pluck('name'),
            ],
            'permissions' => $permissions, // 实时权限列表
        ]);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        auth()->logout();

        return $this->success(null, '退出成功');
    }

    /**
     * 刷新token
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * 返回token响应
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user();

        // 实时加载权限
        $user->load(['roles.permissions']);
        $permissions = $user->getPermissions();

        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
            ],
            'permissions' => $permissions, // 返回实时权限
        ], '登录成功');
    }
}
