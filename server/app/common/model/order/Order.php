<?php
namespace app\common\model\order;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Order extends BaseModel
{
    use SoftDelete;
    
    protected $name = 'order';
    protected $deleteTime = 'delete_time';

    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }
}
