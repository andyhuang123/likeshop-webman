<?php
namespace app\common\model\product;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use think\model\relation\HasMany;

class ProductCate extends BaseModel
{
    use SoftDelete;

    protected $name = 'product_cate';
    protected $deleteTime = 'delete_time';

    protected $schema = [
        'id' => 'int',
        'name' => 'string',
        'sort' => 'int',
        'is_show' => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
        'delete_time' => 'int',
    ];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'cid', 'id');
    }

    public function getIsShowDescAttr($value, $data)
    {
        return $data['is_show'] ? '启用' : '停用';
    }

    public function getProductCountAttr($value, $data)
    {
        return Product::where(['cid' => $data['id']])->count('id');
    }
}

