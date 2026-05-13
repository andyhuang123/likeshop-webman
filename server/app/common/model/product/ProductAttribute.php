<?php
namespace app\common\model\product;

use app\common\model\BaseModel;
use think\model\relation\BelongsTo;
use think\model\relation\HasMany;

class ProductAttribute extends BaseModel
{
    protected $name = 'product_attribute';
    
    protected $schema = [
        'id' => 'int',
        'product_id' => 'int',
        'attribute_name' => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id', 'id');
    }
}
