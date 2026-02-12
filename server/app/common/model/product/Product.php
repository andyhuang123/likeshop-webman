<?php
namespace app\common\model\product;

use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;
use think\model\relation\BelongsTo;

class Product extends BaseModel
{
    use SoftDelete;

    protected $name = 'product';
    protected $deleteTime = 'delete_time';

    protected $schema = [
        'id' => 'int',
        'name' => 'string',
        'sku' => 'string',
        'cid' => 'int',
        'price' => 'float',
        'stock' => 'int',
        'image' => 'string',
        'desc' => 'string',
        'sort' => 'int',
        'is_show' => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
        'delete_time' => 'int',
    ];

    public function cate(): BelongsTo
    {
        return $this->belongsTo(ProductCate::class, 'cid', 'id');
    }

    public function getIsShowDescAttr($value, $data)
    {
        return $data['is_show'] ? '启用' : '停用';
    }

    public function getImageUrlAttr($value, $data)
    {
        return $data['image'] ? FileService::getFileUrl($data['image']) : '';
    }
}

