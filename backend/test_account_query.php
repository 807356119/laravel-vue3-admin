<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Account;
use App\Models\User;

echo "=== 账户列表调试工具 ===\n\n";

// 1. 检查所有用户
echo "【1】所有用户列表：\n";
$users = User::all(['id', 'name', 'email']);
foreach ($users as $user) {
    echo "  - ID: {$user->id}, 名称: {$user->name}, 邮箱: {$user->email}\n";
}
echo "\n";

// 2. 检查所有账户
echo "【2】所有账户列表：\n";
$accounts = Account::all(['id', 'user_id', 'title', 'type']);
if ($accounts->isEmpty()) {
    echo "  ⚠️ 数据库中没有任何账户数据！\n";
} else {
    foreach ($accounts as $account) {
        echo "  - ID: {$account->id}, user_id: {$account->user_id}, 标题: {$account->title}, 类型: {$account->type}\n";
    }
}
echo "\n";

// 3. 检查每个用户的账户数量
echo "【3】每个用户的账户数量：\n";
foreach ($users as $user) {
    $count = Account::where('user_id', $user->id)->count();
    echo "  - 用户 {$user->name} (ID: {$user->id}): {$count} 个账户\n";
}
echo "\n";

// 4. 模拟API查询（假设用户ID=1）
echo "【4】模拟用户ID=1的查询：\n";
$userId = 1;
$testAccounts = Account::where('user_id', $userId)
    ->orderBy('is_favorite', 'desc')
    ->orderBy('created_at', 'desc')
    ->get(['id', 'title', 'type', 'user_id']);

if ($testAccounts->isEmpty()) {
    echo "  ❌ 用户ID={$userId} 查不到任何账户\n";
    echo "  可能原因：\n";
    echo "  1. 创建账户时 user_id 字段未正确赋值\n";
    echo "  2. 登录的用户ID和创建账户时的 user_id 不一致\n";
} else {
    echo "  ✅ 找到 {$testAccounts->count()} 个账户：\n";
    foreach ($testAccounts as $account) {
        echo "    - ID: {$account->id}, 标题: {$account->title}, user_id: {$account->user_id}\n";
    }
}
echo "\n";

// 5. 检查最近创建的账户
echo "【5】最近创建的5个账户：\n";
$recentAccounts = Account::orderBy('created_at', 'desc')->limit(5)->get(['id', 'user_id', 'title', 'created_at']);
if ($recentAccounts->isEmpty()) {
    echo "  ⚠️ 没有账户数据\n";
} else {
    foreach ($recentAccounts as $account) {
        echo "  - ID: {$account->id}, user_id: {$account->user_id}, 标题: {$account->title}, 创建时间: {$account->created_at}\n";
    }
}
echo "\n";

echo "=== 调试完成 ===\n";
