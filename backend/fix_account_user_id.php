<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Account;
use App\Models\User;

echo "=== 修复账户 user_id ===\n\n";

// 获取admin用户
$admin = User::where('email', 'admin@example.com')->first();

if (!$admin) {
    echo "❌ 找不到 admin@example.com 用户\n";
    exit(1);
}

echo "找到管理员用户：\n";
echo "  - ID: {$admin->id}\n";
echo "  - 名称: {$admin->name}\n";
echo "  - 邮箱: {$admin->email}\n\n";

// 查找所有 user_id 不正确的账户
$accounts = Account::all();

echo "数据库中的账户：\n";
foreach ($accounts as $account) {
    echo "  - ID: {$account->id}, user_id: {$account->user_id}, 标题: {$account->title}\n";
}
echo "\n";

// 询问是否要将所有账户的 user_id 改为 admin 的 ID
echo "是否要将所有账户的 user_id 改为 {$admin->id}? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$answer = trim($line);

if (strtolower($answer) === 'y' || strtolower($answer) === 'yes') {
    echo "\n开始修复...\n";

    foreach ($accounts as $account) {
        if ($account->user_id !== $admin->id) {
            $oldUserId = $account->user_id;
            $account->user_id = $admin->id;
            $account->save();
            echo "  ✅ 账户 ID:{$account->id} user_id 从 {$oldUserId} 改为 {$admin->id}\n";
        } else {
            echo "  ⏭️ 账户 ID:{$account->id} user_id 已经是 {$admin->id}，跳过\n";
        }
    }

    echo "\n✅ 修复完成！\n";
} else {
    echo "\n❌ 取消操作\n";
}

echo "\n当前状态：\n";
$adminAccounts = Account::where('user_id', $admin->id)->get();
echo "admin@example.com (ID:{$admin->id}) 的账户数量: {$adminAccounts->count()}\n";

fclose($handle);
