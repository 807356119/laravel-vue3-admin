#!/bin/bash

echo "================================"
echo "管理后台系统 - 前端启动脚本"
echo "================================"

cd frontend

# 检查依赖是否已安装
if [ ! -d "node_modules" ]; then
    echo "正在安装依赖..."
    npm install
fi

# 检查 .env 文件
if [ ! -f ".env" ]; then
    echo "创建 .env 文件..."
    echo "VITE_API_BASE_URL=http://localhost:8000/api" > .env
fi

echo "启动前端开发服务器..."
npm run dev
