#!/bin/bash

echo "================================"
echo "管理后台系统 - 后端启动脚本"
echo "================================"

cd backend

# 检查vendor目录
if [ ! -d "vendor" ]; then
    echo "正在安装依赖..."
    composer install
fi

# 检查.env文件
if [ ! -f ".env" ]; then
    echo "创建 .env 文件..."
    cp .env.example .env
    php artisan key:generate
    php artisan jwt:secret
fi

echo ""
echo "提示：请先配置 .env 中的数据库连接，然后运行："
echo "  php artisan migrate"
echo "  php artisan db:seed"
echo ""
echo "启动后端开发服务器..."
php artisan serve
