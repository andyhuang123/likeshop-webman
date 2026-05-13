<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Webman\Config;
use support\App;
use Webman\ThinkOrm\ThinkOrm;
use app\common\model\auth\Admin;

// 1. 加载 .env
if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/.env')) {
    if (method_exists('Dotenv\Dotenv', 'createUnsafeMutable')) {
        Dotenv::createUnsafeMutable(__DIR__)->load();
    } else {
        Dotenv::createMutable(__DIR__)->load();
    }
}

// 2. 加载配置
Config::clear();
App::loadAllConfig(['route']);

// 3. 加载函数库 (需要其中的 create_password)
require_once __DIR__ . '/app/functions.php';

// 4. 初始化数据库
if (class_exists(ThinkOrm::class)) {
    ThinkOrm::start(null);
} else {
    die("错误: 找不到 Webman\ThinkOrm\ThinkOrm 类，请确认依赖已安装。\n");
}

// 5. 获取参数
$account = $argv[1] ?? 'admin';
$password = $argv[2] ?? '123456';

echo "----------------------------------------\n";
echo "准备重置账号: {$account}\n";
echo "新密码: {$password}\n";
echo "----------------------------------------\n";

try {
    // 6. 查找用户
    $admin = Admin::where('account', $account)->find();
    
    if (!$admin) {
        echo "错误: 数据库中找不到账号 '{$account}'。\n";
        exit(1);
    }

    // 7. 获取 Salt
    $salt = Config::get('project.unique_identification');
    if (!$salt) {
        $salt = getenv('UNIQUE_IDENTIFICATION');
    }
    
    if (!$salt) {
        echo "错误: 无法获取 UNIQUE_IDENTIFICATION (密码盐)。\n";
        exit(1);
    }

    // 8. 重置密码
    $newPasswordEncrypted = create_password($password, $salt);
    $admin->password = $newPasswordEncrypted;
    $admin->save();

    echo "✅ 成功! 账号 '{$account}' 的密码已重置。\n";
    echo "请使用新密码登录后台。\n";

} catch (\Throwable $e) {
    echo "❌ 发生异常: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
