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
        'sku' => 'length:0,64',
        'cid' => 'require|checkProductCate',
        'price' => 'require|float|egt:0',
        'stock' => 'integer|egt:0',
        'image' => 'length:0,255',
        'desc' => 'length:0,1000',
        'sort' => 'egt:0',
        'is_show' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require' => '商品id不能为空',
        'name.require' => '商品名称不能为空',
        'name.length' => '商品名称长度须在1-90位字符',
        'cid.require' => '商品分类不能为空',
        'price.require' => '商品价格不能为空',
        'price.float' => '商品价格格式不正确',
        'price.egt' => '商品价格必须大于等于0',
        'stock.integer' => '库存必须为整数',
        'stock.egt' => '库存必须大于等于0',
        'is_show.in' => '状态不正确',
    ];

    public function sceneAdd(): ProductValidate
    {
        return $this->remove(['id'])
            ->remove('id', 'require|checkProduct');
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
        return $this->only(['id', 'is_show']);
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

