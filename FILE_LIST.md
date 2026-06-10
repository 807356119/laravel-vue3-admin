# 项目文件清单

## 前端文件（frontend/）

### 核心配置
- `package.json` - 项目依赖配置
- `.env` - 环境变量配置
- `vite.config.js` - Vite 构建配置
- `src/main.js` - 应用入口
- `src/App.vue` - 根组件

### 路由与状态管理
- `src/router/index.js` - 路由配置（含权限守卫）
- `src/stores/user.js` - 用户状态管理（Pinia）

### API 接口
- `src/api/auth.js` - 认证接口
- `src/api/user.js` - 用户管理接口
- `src/api/role.js` - 角色管理接口
- `src/api/system.js` - 系统设置接口
- `src/utils/request.js` - Axios 请求封装

### 页面组件
- `src/views/Layout.vue` - 主布局
- `src/views/Dashboard.vue` - 首页仪表盘
- `src/views/login/index.vue` - 登录页
- `src/views/user/index.vue` - 用户管理页
- `src/views/role/index.vue` - 角色管理页
- `src/views/system/index.vue` - 系统设置页

## 后端文件（backend-api/）

### 控制器
- `app/Http/Controllers/AuthController.php` - 认证控制器
- `app/Http/Controllers/UserController.php` - 用户管理控制器
- `app/Http/Controllers/RoleController.php` - 角色管理控制器
- `app/Http/Controllers/PermissionController.php` - 权限管理控制器
- `app/Http/Controllers/SystemSettingController.php` - 系统设置控制器

### 模型
- `app/Models/User.php` - 用户模型
- `app/Models/Role.php` - 角色模型
- `app/Models/Permission.php` - 权限模型
- `app/Models/SystemSetting.php` - 系统设置模型

### 数据库迁移
- `database/migrations/2024_01_01_000001_create_users_table.php`
- `database/migrations/2024_01_01_000002_create_roles_table.php`
- `database/migrations/2024_01_01_000003_create_permissions_table.php`
- `database/migrations/2024_01_01_000004_create_user_roles_table.php`
- `database/migrations/2024_01_01_000005_create_role_permissions_table.php`
- `database/migrations/2024_01_01_000006_create_system_settings_table.php`

### 数据填充
- `database/seeders/DatabaseSeeder.php` - 初始数据（管理员、角色、权限）

### 配置与路由
- `routes/api.php` - API 路由定义
- `config/jwt.php` - JWT 配置
- `app/Http/Middleware/Cors.php` - CORS 中间件
- `.env.example` - 环境变量示例
- `composer.json` - 依赖配置

## 启动脚本
- `start-frontend.sh` - Linux/Mac 前端启动脚本
- `start-frontend.bat` - Windows 前端启动脚本

## 文档
- `README.md` - 完整部署文档
- `FILE_LIST.md` - 本文件清单

## 数据库结构

### users（用户表）
- id, name, email, password, status, created_at, updated_at

### roles（角色表）
- id, name, description, created_at, updated_at

### permissions（权限表）
- id, name, code, parent_id, sort, created_at, updated_at

### user_roles（用户角色关联表）
- id, user_id, role_id, created_at, updated_at

### role_permissions（角色权限关联表）
- id, role_id, permission_id, created_at, updated_at

### system_settings（系统设置表）
- id, key, value, description, created_at, updated_at

## 默认权限列表

1. 用户管理
   - user.view（查看用户）
   - user.create（创建用户）
   - user.edit（编辑用户）
   - user.delete（删除用户）

2. 角色管理
   - role.view（查看角色）
   - role.create（创建角色）
   - role.edit（编辑角色）
   - role.delete（删除角色）

3. 系统设置
   - system.manage（系统管理）

## 技术特点

✅ 前后端完全分离
✅ JWT Token 无状态认证
✅ RBAC 角色权限控制
✅ RESTful API 设计
✅ Vue 3 Composition API
✅ Element Plus UI 组件库
✅ 响应式布局设计
✅ 路由权限守卫
✅ Axios 请求拦截
✅ 状态管理（Pinia）
