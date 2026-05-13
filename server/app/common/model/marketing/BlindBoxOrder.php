<?php
namespace app\common\model\marketing;

use app\common\model\BaseModel;
use app\common\model\product\Product;
use app\common\model\user\User;

class BlindBoxOrder extends BaseModel
{
    protected $name = 'blind_box_order';

    // 关联用户
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // 关联盲盒
    public function blindBox()
    {
        return $this->belongsTo(BlindBox::class, 'blind_box_id', 'id');
    }

    // 关联中奖商品
    public function winProduct()
    {
        return $this->belongsTo(Product::class, 'win_product_id', 'id');
    }
}
