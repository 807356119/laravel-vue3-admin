# 管理后台系统 - 完整部署文档

这是一个基于 **Vue 3 + Laravel 10 + JWT** 的前后端分离管理后台系统。

## 功能特性

- ✅ JWT Token 身份认证
- ✅ 用户管理（增删改查、状态管理、密码重置）
- ✅ 角色管理（RBAC权限控制）
- ✅ 权限管理（树形权限结构）
- ✅ 系统设置（动态配置管理）
- ✅ 响应式布局
- ✅ 前后端完全分离

## 技术栈

### 后端
- Laravel 10
- PHP 8.0+
- MySQL 5.7+
- tymon/jwt-auth (JWT认证)

### 前端
- Vue 3
- Vite
- Vue Router (路由管理)
- Pinia (状态管理)
- Element Plus (UI组件库)
- Axios (HTTP客户端)

## 项目结构

```
my-admin/
├── backend-api/              # Laravel 后端 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/  # 控制器
│   │   │   └── Middleware/   # 中间件
│   │   └── Models/           # 模型
│   ├── config/               # 配置文件
│   ├── database/
│   │   ├── migrations/       # 数据库迁移
│   │   └── seeders/          # 数据填充
│   └── routes/               # 路由定义
│
└── frontend/                 # Vue 3 前端
    ├── src/
    │   ├── api/              # API 接口
    │   ├── components/       # 组件
    │   ├── router/           # 路由
    │   ├── stores/           # Pinia 状态
    │   ├── utils/            # 工具函数
    │   └── views/            # 页面视图
    └── package.json
```

## 后端部署步骤

### 1. 环境要求

- PHP >= 8.0
- Composer
- MySQL >= 5.7
- 扩展：PDO, OpenSSL, Mbstring, Tokenizer, XML, JSON

### 2. 将 backend-api 部署到完整的 Laravel 项目

由于环境限制，`backend-api` 目录包含核心文件。您需要：

**方案 A：在新服务器上创建完整Laravel项目**

```bash
# 创建新的 Laravel 10 项目
composer create-project laravel/laravel my-admin-backend

# 将 backend-api 中的文件复制到新项目
cp -r backend-api/app/* my-admin-backend/app/
cp -r backend-api/config/* my-admin-backend/config/
cp -r backend-api/database/* my-admin-backend/database/
cp -r backend-api/routes/* my-admin-backend/routes/
cp backend-api/.env.example my-admin-backend/.env.example

cd my-admin-backend
```

**方案 B：使用提供的文件继续配置**

```bash
cd backend-api
```

### 3. 安装依赖

```bash
composer install

# 如果遇到平台要求问题
composer install --ignore-platform-reqs
```

### 4. 配置环境

```bash
# 复制环境配置文件
cp .env.example .env

# 生成应用密钥
php artisan key:generate

# 生成 JWT 密钥
php artisan jwt:secret
```

### 5. 配置数据库

编辑 `.env` 文件：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

创建数据库：

```bash
mysql -u root -p
CREATE DATABASE admin_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. 运行迁移和填充数据

```bash
# 运行数据库迁移
php artisan migrate

# 填充初始数据
php artisan db:seed
```

### 7. 配置 JWT 和 CORS

在 `config/auth.php` 中添加：

```php
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

在 `app/Http/Kernel.php` 的 `$middleware` 数组中添加：

```php
\App\Http\Middleware\Cors::class,
```

### 8. 启动后端服务

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

后端 API 地址：`http://localhost:8000/api`

## 前端部署步骤

### 1. 安装依赖

```bash
cd frontend
npm install
```

### 2. 配置 API 地址

编辑 `frontend/.env` 文件：

```env
VITE_API_BASE_URL=http://localhost:8000/api
```

如果后端部署在其他服务器，修改为实际地址。

### 3. 启动开发服务器

```bash
npm run dev
```

前端访问地址：`http://localhost:5173`

### 4. 生产环境构建

```bash
npm run build
```

构建产物在 `dist/` 目录，可部署到 Nginx/Apache。

## Nginx 生产环境配置

### 后端 Nginx 配置

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    root /path/to/my-admin-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 前端 Nginx 配置

```nginx
server {
    listen 80;
    server_name admin.yourdomain.com;
    root /path/to/frontend/dist;

    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## 默认账号

系统已预置两个测试账号：

**管理员账号：**
- 邮箱：`admin@example.com`
- 密码：`123456`
- 权限：所有权限

**普通用户账号：**
- 邮箱：`user@example.com`
- 密码：`123456`
- 权限：查看权限

## API 文档

### 认证相关

- `POST /api/auth/login` - 用户登录
- `POST /api/auth/register` - 用户注册
- `POST /api/auth/logout` - 退出登录
- `POST /api/auth/refresh` - 刷新Token
- `GET /api/auth/me` - 获取当前用户信息

### 用户管理

- `GET /api/users` - 获取用户列表
- `POST /api/users` - 创建用户
- `GET /api/users/{id}` - 获取用户详情
- `PUT /api/users/{id}` - 更新用户
- `DELETE /api/users/{id}` - 删除用户
- `POST /api/users/{id}/toggle-status` - 切换用户状态
- `POST /api/users/{id}/reset-password` - 重置密码

### 角色管理

- `GET /api/roles` - 获取角色列表
- `POST /api/roles` - 创建角色
- `GET /api/roles/{id}` - 获取角色详情
- `PUT /api/roles/{id}` - 更新角色
- `DELETE /api/roles/{id}` - 删除角色
- `POST /api/roles/{id}/permissions` - 分配权限

### 权限管理

- `GET /api/permissions` - 获取权限树
- `GET /api/permissions/all` - 获取所有权限

### 系统设置

- `GET /api/settings` - 获取系统设置
- `PUT /api/settings` - 更新系统设置

## 常见问题

### 1. CORS 错误

确保后端已配置 CORS 中间件，或在 Nginx 添加 CORS 头。

### 2. JWT Token 过期

默认有效期 60 分钟，可在 `.env` 中修改 `JWT_TTL`。

### 3. 数据库连接失败

检查 `.env` 中的数据库配置和MySQL服务状态。

### 4. 权限问题

```bash
# Laravel 需要写入权限
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 开发建议

1. 修改默认密码
2. 根据需求调整权限结构
3. 添加日志记录
4. 配置定时任务（如果需要）
5. 启用 HTTPS
6. 配置生产环境的错误处理

## 许可证

MIT License

## 联系方式

如有问题，请提交 Issue 或联系开发者。
