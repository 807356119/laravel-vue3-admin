@echo off
echo ================================
echo 管理后台系统 - 前端启动脚本
echo ================================

cd frontend

REM 检查依赖是否已安装
if not exist "node_modules" (
    echo 正在安装依赖...
    call npm install
)

REM 检查 .env 文件
if not exist ".env" (
    echo 创建 .env 文件...
    echo VITE_API_BASE_URL=http://localhost:8000/api > .env
)

echo 启动前端开发服务器...
call npm run dev

pause
