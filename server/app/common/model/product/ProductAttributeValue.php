<?php
namespace app\common\model\product;

use app\common\model\BaseModel;
use think\model\relation\BelongsTo;

class ProductAttributeValue extends BaseModel
{
    protected $name = 'product_attribute_value';
    
    protected $schema = [
        'id' => 'int',
        'attribute_id' => 'int',
        'attribute_value' => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id', 'id');
    }
}
