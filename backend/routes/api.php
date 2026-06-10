<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| API Routes - RESTful 规范 + 动态权限控制
|--------------------------------------------------------------------------
*/

// ==================== 公开路由（无需认证） ====================
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

// ==================== 需要认证的路由 ====================
Route::middleware('auth:api')->group(function () {

    // 认证相关（无需权限）
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('me', [AuthController::class, 'me'])->name('me');
    });

    // 用户管理 - 带权限控制
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:user.view');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:user.create');
        Route::get('{id}', [UserController::class, 'show'])->middleware('permission:user.view');
        Route::put('{id}', [UserController::class, 'update'])->middleware('permission:user.edit');
        Route::delete('{id}', [UserController::class, 'destroy'])->middleware('permission:user.delete');
        Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:user.edit');
        Route::post('{id}/reset-password', [UserController::class, 'resetPassword'])->middleware('permission:user.edit');
    });

    // 角色管理 - 带权限控制
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:role.view');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:role.create');
        Route::get('{id}', [RoleController::class, 'show'])->middleware('permission:role.view');
        Route::put('{id}', [RoleController::class, 'update'])->middleware('permission:role.edit');
        Route::delete('{id}', [RoleController::class, 'destroy'])->middleware('permission:role.delete');
        Route::post('{id}/permissions', [RoleController::class, 'assignPermissions'])->middleware('permission:role.edit');
    });

    // 权限管理 - 需要系统管理权限
    Route::prefix('permissions')->name('permissions.')->middleware('permission:system.manage')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('all', [PermissionController::class, 'all']);
        Route::get('tree', [PermissionController::class, 'tree']);
    });

    // 系统设置 - 需要系统管理权限
    Route::prefix('settings')->name('settings.')->middleware('permission:system.manage')->group(function () {
        Route::get('/', [SystemSettingController::class, 'index']);
        Route::put('/', [SystemSettingController::class, 'update']);
        Route::get('{key}', [SystemSettingController::class, 'show']);
    });

    // 账户管理 - 个人数据，只需要登录即可
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [AccountController::class, 'index']);
        Route::post('/', [AccountController::class, 'store']);
        Route::get('{id}', [AccountController::class, 'show']);
        Route::put('{id}', [AccountController::class, 'update']);
        Route::delete('{id}', [AccountController::class, 'destroy']);
        Route::post('{id}/toggle-favorite', [AccountController::class, 'toggleFavorite']);
        Route::get('{id}/password', [AccountController::class, 'getPassword']);
        Route::post('upload-image', [AccountController::class, 'uploadImage']);
    });
});
