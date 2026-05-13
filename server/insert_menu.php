<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Webman\Config;
use support\App;
use Webman\ThinkOrm\ThinkOrm;
use app\common\model\auth\SystemMenu;

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

// 3. 初始化数据库
if (class_exists(ThinkOrm::class)) {
    ThinkOrm::start(null);
} else {
    die("错误: 找不到 Webman\ThinkOrm\ThinkOrm 类。\n");
}

echo "开始添加商品管理菜单...\n";

// 4. 定义菜单数据
$menus = [
    [
        'type' => 'M', // 目录
        'name' => '商品管理',
        'icon' => 'el-icon-Goods',
        'sort' => 10,
        'paths' => 'product',
        'component' => '',
        'perms' => '',
        'is_show' => 1,
        'is_disable' => 0,
        'children' => [
            [
                'type' => 'C', // 菜单
                'name' => '商品分类',
                'icon' => 'el-icon-Menu',
                'sort' => 1,
                'paths' => 'product/category',
                'component' => 'product/category/index',
                'perms' => 'product.category/index',
                'is_show' => 1,
                'is_disable' => 0,
            ],
            [
                'type' => 'C', // 菜单
                'name' => '商品列表',
                'icon' => 'el-icon-Goods',
                'sort' => 2,
                'paths' => 'product/lists',
                'component' => 'product/lists/index',
                'perms' => 'product.product/index',
                'is_show' => 1,
                'is_disable' => 0,
                'children' => [ // 商品列表下的功能按钮
                     [
                        'type' => 'A',
                        'name' => '商品列表',
                        'perms' => 'product.product/lists',
                    ],
                    [
                        'type' => 'A',
                        'name' => '商品添加',
                        'perms' => 'product.product/add',
                    ],
                    [
                        'type' => 'A',
                        'name' => '商品编辑',
                        'perms' => 'product.product/edit',
                    ],
                    [
                        'type' => 'A',
                        'name' => '商品删除',
                        'perms' => 'product.product/delete',
                    ],
                    [
                        'type' => 'A',
                        'name' => '更改状态',
                        'perms' => 'product.product/status',
                    ],
                ]
            ],
        ]
    ]
];

function createMenu($data, $pid = 0) {
    foreach ($data as $item) {
        // 检查是否存在同名同级菜单
        $exists = SystemMenu::where('name', $item['name'])
            ->where('pid', $pid)
            ->find();

        if ($exists) {
            echo "菜单已存在: {$item['name']} (ID: {$exists->id})\n";
            $currentId = $exists->id;
        } else {
            $menu = new SystemMenu();
            $menu->pid = $pid;
            $menu->type = $item['type'];
            $menu->name = $item['name'];
            $menu->icon = $item['icon'] ?? '';
            $menu->sort = $item['sort'] ?? 0;
            $menu->paths = $item['paths'] ?? '';
            $menu->component = $item['component'] ?? '';
            $menu->perms = $item['perms'] ?? '';
            $menu->is_show = $item['is_show'] ?? 1;
            $menu->is_disable = $item['is_disable'] ?? 0;
            $menu->create_time = time();
            $menu->update_time = time();
            $menu->save();
            
            echo "✅ 创建菜单: {$item['name']} (ID: {$menu->id})\n";
            $currentId = $menu->id;
        }

        if (isset($item['children']) && !empty($item['children'])) {
            createMenu($item['children'], $currentId);
        }
    }
}

try {
    createMenu($menus);
    echo "商品管理菜单添加完成！\n";
} catch (\Throwable $e) {
    echo "❌ 发生错误: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
