<?php
namespace app\adminapi\logic\product;

use app\common\logic\BaseLogic;
use app\common\model\product\Product;
use Exception;

class ProductLogic extends BaseLogic
{
    public static function add(array $params): void
    {
        Product::create([
            'name' => $params['name'],
            'sku' => $params['sku'] ?? '',
            'cid' => $params['cid'],
            'price' => $params['price'],
            'stock' => $params['stock'] ?? 0,
            'image' => $params['image'] ?? '',
            'desc' => $params['desc'] ?? '',
            'sort' => $params['sort'] ?? 0,
            'is_show' => $params['is_show'],
        ]);
    }

    public static function edit(array $params): bool
    {
        try {
            Product::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'sku' => $params['sku'] ?? '',
                'cid' => $params['cid'],
                'price' => $params['price'],
                'stock' => $params['stock'] ?? 0,
                'image' => $params['image'] ?? '',
                'desc' => $params['desc'] ?? '',
                'sort' => $params['sort'] ?? 0,
                'is_show' => $params['is_show'],
            ]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function delete(array $params): void
    {
        Product::destroy($params['id']);
    }

    public static function detail($params): array
    {
        return Product::findOrEmpty($params['id'])
            ->append(['image_url', 'is_show_desc'])
            ->toArray();
    }

    public static function updateStatus(array $params): bool
    {
        Product::update([
            'id' => $params['id'],
            'is_show' => $params['is_show'],
        ]);
        return true;
    }
}

