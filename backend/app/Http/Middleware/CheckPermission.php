<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class CheckPermission
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        // 获取当前用户
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => '未授权访问',
                'timestamp' => now()->toIso8601String(),
            ], 401);
        }

        // 实时从数据库获取用户权限（不使用缓存）
        $user->load(['roles.permissions']);

        // 检查用户是否有该权限
        if (!$this->hasPermission($user, $permission)) {
            return response()->json([
                'code' => 403,
                'message' => '无权限访问此资源',
                'required_permission' => $permission,
                'timestamp' => now()->toIso8601String(),
            ], 403);
        }

        return $next($request);
    }

    /**
     * 检查用户是否拥有指定权限
     *
     * @param  $user
     * @param  string  $permission
     * @return bool
     */
    private function hasPermission($user, string $permission): bool
    {
        // 遍历用户的所有角色
        foreach ($user->roles as $role) {
            // 遍历角色的所有权限
            foreach ($role->permissions as $perm) {
                if ($perm->code === $permission) {
                    return true;
                }
            }
        }

        return false;
    }
}
