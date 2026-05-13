<?php
namespace app\common\model\product;

use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\relation\BelongsTo;

class ProductSku extends BaseModel
{
    protected $name = 'product_sku';
    
    protected $schema = [
        'id' => 'int',
        'product_id' => 'int',
        'sku_code' => 'string',
        'attribute_value_ids' => 'string',
        'attribute_value_names' => 'string',
        'price' => 'float',
        'stock' => 'int',
        'image' => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
        'market_price' => 'float',
        'cost_price' => 'float',
        'weight' => 'float',
        'volume' => 'float',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function getImageUrlAttr($value, $data)
    {
        return $data['image'] ? FileService::getFileUrl($data['image']) : '';
    }
}
