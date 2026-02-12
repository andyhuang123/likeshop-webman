<?php
namespace app\adminapi\validate\product;

use app\common\validate\BaseValidate;
use app\common\model\product\ProductCate;
use app\common\model\product\Product;

class ProductCateValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkProductCate',
        'name' => 'require|length:1,90',
        'is_show' => 'require|in:0,1',
        'sort' => 'egt:0',
    ];

    protected $message = [
        'id.require' => '商品分类id不能为空',
        'name.require' => '商品分类不能为空',
        'name.length' => '商品分类长度须在1-90位字符',
        'sort.egt' => '排序值不正确',
    ];

    public function sceneAdd(): ProductCateValidate
    {
        return $this->remove(['id'])
            ->remove('id', 'require|checkProductCate');
    }

    public function sceneDetail(): ProductCateValidate
    {
        return $this->only(['id']);
    }

    public function sceneStatus(): ProductCateValidate
    {
        return $this->only(['id', 'is_show']);
    }

    public function sceneEdit()
    {
    }

    public function sceneDelete(): ProductCateValidate
    {
        return $this->only(['id'])
            ->append('id', 'checkDeleteProductCate');
    }

    public function checkProductCate($value): bool|string
    {
        $cate = ProductCate::findOrEmpty($value);
        if ($cate->isEmpty()) {
            return '商品分类不存在';
        }
        return true;
    }

    public function checkDeleteProductCate($value): bool|string
    {
        $product = Product::where('cid', $value)->findOrEmpty();
        if (!$product->isEmpty()) {
            return '商品分类已使用，请先删除绑定该分类的商品';
        }
        return true;
    }
}

