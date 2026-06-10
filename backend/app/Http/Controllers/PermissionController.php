<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * 获取权限列表（树形结构）
     */
    public function index()
    {
        $permissions = Permission::whereNull('parent_id')
            ->with('children')
            ->get();

        return response()->json(['data' => $permissions]);
    }

    /**
     * 获取所有权限（扁平结构）
     */
    public function all()
    {
        $permissions = Permission::all();

        return response()->json(['data' => $permissions]);
    }
}
