<?php
namespace app\common\model\product;

use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;
use think\model\relation\BelongsTo;
use think\model\relation\HasMany;

class Product extends BaseModel
{
    use SoftDelete;

    protected $name = 'product';
    protected $deleteTime = 'delete_time';

    protected $schema = [
        'id' => 'int',
        'name' => 'string',
        'sku' => 'string',
        'category_id' => 'int',
        'price' => 'float',
        'stock' => 'int',
        'main_image' => 'string',
        'description' => 'string',
        'sort' => 'int',
        'status' => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
        'delete_time' => 'int',
        'market_price' => 'float',
        'cost_price' => 'float',
        'weight' => 'float',
        'volume' => 'float',
        'brand_id' => 'int',
        'product_images' => 'string',
    ];

    protected $field = [
        'cid' => 'category_id',
        'image' => 'main_image',
        'desc' => 'description',
        'is_show' => 'status',
    ];

    public function setProductImagesAttr($value)
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    public function getProductImagesAttr($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }

    public function cate(): BelongsTo
    {
        return $this->belongsTo(ProductCate::class, 'category_id', 'id');
    }

    public function skus(): HasMany
    {
        return $this->hasMany(ProductSku::class, 'product_id', 'id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id');
    }

    public function getIsShowDescAttr($value, $data)
    {
        return $data['status'] ? '启用' : '停用';
    }

    public function getImageUrlAttr($value, $data)
    {
        return $data['main_image'] ? FileService::getFileUrl($data['main_image']) : '';
    }
}

