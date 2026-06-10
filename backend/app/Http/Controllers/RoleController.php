<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * 获取角色列表
     */
    public function index()
    {
        try {
            $roles = $this->roleService->getAllRoles();

            return response()->json(['data' => $roles]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 创建角色
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
            'description' => 'nullable|string',
        ]);

        try {
            $role = $this->roleService->createRole($validated);

            return response()->json([
                'message' => '角色创建成功',
                'data' => $role
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取单个角色
     */
    public function show($id)
    {
        try {
            $role = $this->roleService->getRoleById($id);

            return response()->json(['data' => $role]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * 更新角色
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:roles,name,' . $id,
            'description' => 'nullable|string',
        ]);

        try {
            $role = $this->roleService->updateRole($id, $validated);

            return response()->json([
                'message' => '角色更新成功',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 删除角色
     */
    public function destroy($id)
    {
        try {
            $this->roleService->deleteRole($id);

            return response()->json(['message' => '角色删除成功']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 分配权限
     */
    public function assignPermissions(Request $request, $id)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role = $this->roleService->assignPermissions($id, $validated['permissions']);

            return response()->json([
                'message' => '权限分配成功',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
