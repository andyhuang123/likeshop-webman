<?php
namespace app\adminapi\validate\product;

use app\common\model\product\Product;
use app\common\model\product\ProductCate;
use app\common\validate\BaseValidate;

class ProductValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkProduct',
        'name' => 'require|length:1,90',
        'category_id' => 'require|checkProductCate',
        'price' => 'require|float|egt:0',
        'market_price' => 'float|egt:0',
        'cost_price' => 'float|egt:0',
        'weight' => 'float|egt:0',
        'volume' => 'float|egt:0',
        'brand_id' => 'integer',
        'product_images' => 'array',
        'stock' => 'integer|egt:0',
        'main_image' => 'length:0,255',
        'description' => 'length:0,1000',
        'sort' => 'egt:0',
        'status' => 'require|in:0,1',
        'spec_type' => 'in:1,2',
        'specs' => 'array',
        'skus' => 'array',
    ];

    protected $message = [
        'id.require' => '商品id不能为空',
        'name.require' => '商品名称不能为空',
        'name.length' => '商品名称长度须在1-90位字符',
        'category_id.require' => '商品分类不能为空',
        'price.require' => '商品价格不能为空',
        'price.float' => '商品价格格式不正确',
        'price.egt' => '商品价格必须大于等于0',
        'market_price.float' => '市场价格格式不正确',
        'market_price.egt' => '市场价格必须大于等于0',
        'cost_price.float' => '成本价格格式不正确',
        'cost_price.egt' => '成本价格必须大于等于0',
        'weight.float' => '重量格式不正确',
        'weight.egt' => '重量必须大于等于0',
        'volume.float' => '体积格式不正确',
        'volume.egt' => '体积必须大于等于0',
        'brand_id.integer' => '品牌ID必须为整数',
        'product_images.array' => '商品图集格式错误',
        'stock.integer' => '库存必须为整数',
        'stock.egt' => '库存必须大于等于0',
        'status.in' => '状态不正确',
        'spec_type.in' => '规格类型不正确',
        'specs.array' => '规格项数据格式错误',
        'skus.array' => '规格明细数据格式错误',
    ];

    public function sceneAdd(): ProductValidate
    {
        return $this->remove('id', true);
    }

    public function sceneEdit()
    {
    }

    public function sceneDetail(): ProductValidate
    {
        return $this->only(['id']);
    }

    public function sceneDelete(): ProductValidate
    {
        return $this->only(['id']);
    }

    public function sceneStatus(): ProductValidate
    {
        return $this->only(['id', 'status']);
    }

    public function checkProductCate($value): bool|string
    {
        $cate = ProductCate::findOrEmpty($value);
        if ($cate->isEmpty()) {
            return '商品分类不存在';
        }
        return true;
    }

    public function checkProduct($value): bool|string
    {
        $product = Product::findOrEmpty($value);
        if ($product->isEmpty()) {
            return '商品不存在';
        }
        return true;
    }
}
