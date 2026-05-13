<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use support\Request;
use Webman\Route;
use app\adminapi\controller\marketing\BlindBoxController;

// 盲盒管理路由
Route::group('/adminapi/marketing/blind_box', function () {
    Route::any('/lists', [BlindBoxController::class, 'lists']);
    Route::any('/add', [BlindBoxController::class, 'add']);
    Route::any('/edit', [BlindBoxController::class, 'edit']);
    Route::any('/delete', [BlindBoxController::class, 'delete']);
    Route::any('/detail', [BlindBoxController::class, 'detail']);
    Route::any('/set_prizes', [BlindBoxController::class, 'setPrizes']);
    Route::any('/status', [BlindBoxController::class, 'status']);
});

// 商品分类路由
Route::group('/adminapi/product/productCate', function () {
    Route::any('/lists', [app\adminapi\controller\product\ProductCateController::class, 'lists']);
    Route::any('/all', [app\adminapi\controller\product\ProductCateController::class, 'all']);
    Route::any('/add', [app\adminapi\controller\product\ProductCateController::class, 'add']);
    Route::any('/edit', [app\adminapi\controller\product\ProductCateController::class, 'edit']);
    Route::any('/delete', [app\adminapi\controller\product\ProductCateController::class, 'delete']);
    Route::any('/detail', [app\adminapi\controller\product\ProductCateController::class, 'detail']);
    Route::any('/updateStatus', [app\adminapi\controller\product\ProductCateController::class, 'updateStatus']);
});

// 商品管理路由
Route::group('/adminapi/product/product', function () {
    Route::any('/lists', [app\adminapi\controller\product\ProductController::class, 'lists']);
    Route::any('/add', [app\adminapi\controller\product\ProductController::class, 'add']);
    Route::any('/edit', [app\adminapi\controller\product\ProductController::class, 'edit']);
    Route::any('/delete', [app\adminapi\controller\product\ProductController::class, 'delete']);
    Route::any('/detail', [app\adminapi\controller\product\ProductController::class, 'detail']);
    Route::any('/updateStatus', [app\adminapi\controller\product\ProductController::class, 'updateStatus']);
});


//404也跨域
Route::fallback(function(Request $request){
    $json = "";
    if ($request->method() !== 'OPTIONS'){
        $json = json_encode(['code' => 404, 'msg' => '404 not found']);
    }
    return response($json,404);
})->middleware([
    \app\common\http\middleware\AllowMiddleware::class
]);