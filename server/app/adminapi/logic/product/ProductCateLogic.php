<?php
namespace app\adminapi\logic\product;

use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\product\ProductCate;
use Exception;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class ProductCateLogic extends BaseLogic
{
    public static function add(array $params): void
    {
        ProductCate::create([
            'name' => $params['name'],
            'is_show' => $params['is_show'],
            'sort' => $params['sort'] ?? 0,
        ]);
    }

    public static function edit(array $params): bool
    {
        try {
            ProductCate::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'is_show' => $params['is_show'],
                'sort' => $params['sort'] ?? 0,
            ]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function delete(array $params): void
    {
        ProductCate::destroy($params['id']);
    }

    public static function detail($params): array
    {
        return ProductCate::findOrEmpty($params['id'])->toArray();
    }

    public static function updateStatus(array $params): bool
    {
        ProductCate::update([
            'id' => $params['id'],
            'is_show' => $params['is_show'],
        ]);
        return true;
    }

    public static function getAllData(): array
    {
        return ProductCate::where(['is_show' => YesNoEnum::YES])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }
}

