<?php
namespace app\common\model\marketing;

use app\common\model\BaseModel;
use app\common\model\product\Product;

class BlindBoxDetail extends BaseModel
{
    protected $name = 'blind_box_detail';

    // 关联商品
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
