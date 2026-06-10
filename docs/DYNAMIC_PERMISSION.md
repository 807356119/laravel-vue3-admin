# 🔐 动态权限控制已实现！

## ✅ 实现的功能

### 1. 实时权限检查
- ✅ 每次请求都从数据库实时查询权限
- ✅ 不使用缓存，权限立即生效
- ✅ 后台修改权限后，前端立刻失去/获得访问权限

### 2. 权限中间件
- ✅ `CheckPermission` 中间件
- ✅ 实时从数据库加载用户权限
- ✅ 权限不足返回 403 错误

### 3. User模型方法
- ✅ `hasPermission($code)` - 检查单个权限
- ✅ `getPermissions()` - 获取所有权限（实时）
- ✅ `hasAnyPermission($array)` - 检查是否有任一权限
- ✅ `hasAllPermissions($array)` - 检查是否拥有所有权限

---

## 🎯 权限代码规范

### 用户管理
- `user.view` - 查看用户
- `user.create` - 创建用户
- `user.edit` - 编辑用户
- `user.delete` - 删除用户

### 角色管理
- `role.view` - 查看角色
- `role.create` - 创建角色
- `role.edit` - 编辑角色
- `role.delete` - 删除角色

### 系统管理
- `system.manage` - 系统管理（权限管理、系统设置）

---

## 📝 路由使用示例

```php
// 需要 user.view 权限才能访问
Route::get('users', [UserController::class, 'index'])
    ->middleware('permission:user.view');

// 需要 user.create 权限
Route::post('users', [UserController::class, 'store'])
    ->middleware('permission:user.create');

// 需要 system.manage 权限
Route::get('permissions', [PermissionController::class, 'index'])
    ->middleware('permission:system.manage');
```

---

## 🔄 工作流程

### 1. 用户访问接口
```
用户请求 → JWT认证 → 权限中间件 → 实时查询数据库 → 检查权限 → 允许/拒绝
```

### 2. 权限修改后立即生效
```
后台修改权限 → 保存到数据库 → 下次请求时 → 重新查询权限 → 立即生效
```

---

## 💡 API响应示例

### 登录成功（包含权限列表）
```json
{
  "code": 200,
  "message": "登录成功",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com"
    },
    "permissions": [
      "user.view",
      "user.create",
      "user.edit",
      "user.delete",
      "role.view",
      "role.create",
      "role.edit",
      "role.delete",
      "system.manage"
    ]
  }
}
```

### 权限不足
```json
{
  "code": 403,
  "message": "无权限访问此资源",
  "required_permission": "user.delete",
  "timestamp": "2026-06-10T15:30:00+08:00"
}
```

### 获取当前用户信息（实时权限）
```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com",
      "roles": ["超级管理员"]
    },
    "permissions": [
      "user.view",
      "user.create",
      "user.edit"
    ]
  }
}
```

---

## 🛠️ 使用方法

### 在控制器中检查权限
```php
// 方式1：使用中间件（推荐）
Route::get('users', [UserController::class, 'index'])
    ->middleware('permission:user.view');

// 方式2：在控制器方法中手动检查
public function index()
{
    if (!auth()->user()->hasPermission('user.view')) {
        return $this->forbidden('无权限访问');
    }
    
    // 业务逻辑...
}

// 方式3：检查多个权限
public function someAction()
{
    // 检查是否有任一权限
    if (!auth()->user()->hasAnyPermission(['user.edit', 'user.delete'])) {
        return $this->forbidden();
    }
    
    // 检查是否拥有所有权限
    if (!auth()->user()->hasAllPermissions(['user.view', 'user.edit'])) {
        return $this->forbidden();
    }
}
```

---

## ⚡ 特点

### 1. 实时性
- ✅ 不使用缓存
- ✅ 每次请求都查询数据库
- ✅ 权限修改后立即生效

### 2. 灵活性
- ✅ 可在路由层面控制
- ✅ 可在控制器层面检查
- ✅ 支持单个/多个权限检查

### 3. 安全性
- ✅ 统一的权限检查逻辑
- ✅ 详细的错误响应
- ✅ 防止越权访问

---

## 🎯 前端配合

前端应该：
1. 登录后保存用户权限列表
2. 根据权限显示/隐藏菜单和按钮
3. 定期调用 `/api/auth/me` 刷新权限
4. 捕获 403 错误，提示用户权限不足

---

## 📋 配置步骤

1. ✅ 中间件已注册到 `Kernel.php`
2. ✅ 路由已添加权限中间件
3. ✅ User模型已添加权限检查方法
4. ✅ AuthController返回实时权限

**权限控制已完全配置完成！** 🔐✨

**后台修改权限后，用户的下一次请求就会立即生效！**
