<?php
namespace app\common\model\marketing;

use app\common\model\BaseModel;
use app\common\model\product\Product;
use app\common\model\user\User;
use think\model\concern\SoftDelete;

class BlindBoxRecord extends BaseModel
{
    use SoftDelete;

    protected $name = 'blind_box_record';
    protected $deleteTime = 'delete_time';

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 关联盲盒
     */
    public function blindBox()
    {
        return $this->belongsTo(BlindBox::class, 'blind_box_id', 'id');
    }

    /**
     * 关联商品
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * 关联盲盒订单
     */
    public function blindBoxOrder()
    {
        return $this->belongsTo(BlindBoxOrder::class, 'order_id', 'id');
    }
}
