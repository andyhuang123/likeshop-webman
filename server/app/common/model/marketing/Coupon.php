<?php
namespace app\common\model\marketing;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Coupon extends BaseModel
{
    use SoftDelete;

    protected $name = 'marketing_coupon';
    protected $deleteTime = 'delete_time';

    /**
     * 获取优惠券类型描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getTypeDescAttr($value, $data)
    {
        $types = [1 => '满减券', 2 => '折扣券'];
        return $types[$data['type']] ?? '未知';
    }

    /**
     * 获取状态描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] == 1 ? '进行中' : '已结束'; // Simplified logic, usually based on time too
    }

    /**
     * 获取发放时间描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getSendTimeDescAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['send_time_start']) . ' ~ ' . date('Y-m-d H:i:s', $data['send_time_end']);
    }

    /**
     * 获取用券时间描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getUseTimeDescAttr($value, $data)
    {
        if ($data['use_time_type'] == 1) {
            return date('Y-m-d H:i:s', $data['use_time_start']) . ' ~ ' . date('Y-m-d H:i:s', $data['use_time_end']);
        } else {
            return '领取后 ' . $data['use_time'] . ' 天内有效';
        }
    }
}
