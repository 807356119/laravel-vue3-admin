@echo off
echo ================================
echo 管理后台系统 - 后端启动脚本
echo ================================

cd backend

REM 检查vendor目录
if not exist "vendor" (
    echo 正在安装依赖...
    call composer install
)

REM 检查.env文件
if not exist ".env" (
    echo 创建 .env 文件...
    copy .env.example .env
    php artisan key:generate
    php artisan jwt:secret
)

echo.
echo 提示：请先配置 .env 中的数据库连接，然后运行：
echo   php artisan migrate
echo   php artisan db:seed
echo.
echo 启动后端开发服务器...
php artisan serve

pause
