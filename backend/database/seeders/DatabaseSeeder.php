<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 创建权限
        $permissions = [
            ['name' => '用户管理', 'code' => 'user', 'parent_id' => null, 'sort' => 1],
            ['name' => '查看用户', 'code' => 'user.view', 'parent_id' => 1, 'sort' => 1],
            ['name' => '创建用户', 'code' => 'user.create', 'parent_id' => 1, 'sort' => 2],
            ['name' => '编辑用户', 'code' => 'user.edit', 'parent_id' => 1, 'sort' => 3],
            ['name' => '删除用户', 'code' => 'user.delete', 'parent_id' => 1, 'sort' => 4],

            ['name' => '角色管理', 'code' => 'role', 'parent_id' => null, 'sort' => 2],
            ['name' => '查看角色', 'code' => 'role.view', 'parent_id' => 6, 'sort' => 1],
            ['name' => '创建角色', 'code' => 'role.create', 'parent_id' => 6, 'sort' => 2],
            ['name' => '编辑角色', 'code' => 'role.edit', 'parent_id' => 6, 'sort' => 3],
            ['name' => '删除角色', 'code' => 'role.delete', 'parent_id' => 6, 'sort' => 4],

            ['name' => '系统设置', 'code' => 'system', 'parent_id' => null, 'sort' => 3],
            ['name' => '系统管理', 'code' => 'system.manage', 'parent_id' => 11, 'sort' => 1],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // 创建角色
        $adminRole = Role::create([
            'name' => '超级管理员',
            'description' => '拥有所有权限'
        ]);

        $userRole = Role::create([
            'name' => '普通用户',
            'description' => '基本权限'
        ]);

        // 给管理员角色分配所有权限
        $adminRole->permissions()->attach(Permission::pluck('id'));

        // 给普通用户角色分配部分权限
        $userRole->permissions()->attach([2, 7]); // 查看用户、查看角色

        // 创建管理员用户
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'status' => 'active',
        ]);

        $admin->roles()->attach($adminRole->id);

        // 创建普通用户
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('123456'),
            'status' => 'active',
        ]);

        $user->roles()->attach($userRole->id);

        // 创建系统设置
        \App\Models\SystemSetting::create([
            'key' => 'site_name',
            'value' => '管理后台系统',
            'description' => '网站名称'
        ]);

        \App\Models\SystemSetting::create([
            'key' => 'contact_email',
            'value' => 'admin@example.com',
            'description' => '联系邮箱'
        ]);
    }
}
