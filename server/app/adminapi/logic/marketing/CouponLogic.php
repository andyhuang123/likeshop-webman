<?php
namespace app\adminapi\logic\marketing;

use app\common\logic\BaseLogic;
use app\common\model\marketing\Coupon;
use think\facade\Db;

class CouponLogic extends BaseLogic
{
    /**
     * @notes 添加优惠券
     * @param array $params
     * @return bool
     */
    public static function add(array $params): bool
    {
        try {
            Coupon::create([
                'name' => $params['name'],
                'type' => $params['type'],
                'money' => $params['money'] ?? 0,
                'discount' => $params['discount'] ?? 0,
                'condition_type' => $params['condition_type'],
                'condition_money' => $params['condition_money'] ?? 0,
                'send_total' => $params['send_total'],
                'get_limit' => $params['get_limit'],
                'send_time_start' => $params['send_time_start'],
                'send_time_end' => $params['send_time_end'],
                'use_time_type' => $params['use_time_type'],
                'use_time_start' => $params['use_time_start'] ?? 0,
                'use_time_end' => $params['use_time_end'] ?? 0,
                'use_time' => $params['use_time'] ?? 0,
                'status' => $params['status'],
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 编辑优惠券
     * @param array $params
     * @return bool
     */
    public static function edit(array $params): bool
    {
        try {
            Coupon::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'type' => $params['type'],
                'money' => $params['money'] ?? 0,
                'discount' => $params['discount'] ?? 0,
                'condition_type' => $params['condition_type'],
                'condition_money' => $params['condition_money'] ?? 0,
                'send_total' => $params['send_total'],
                'get_limit' => $params['get_limit'],
                'send_time_start' => $params['send_time_start'],
                'send_time_end' => $params['send_time_end'],
                'use_time_type' => $params['use_time_type'],
                'use_time_start' => $params['use_time_start'] ?? 0,
                'use_time_end' => $params['use_time_end'] ?? 0,
                'use_time' => $params['use_time'] ?? 0,
                'status' => $params['status'],
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除优惠券
     * @param array $params
     * @return bool
     */
    public static function delete(array $params): bool
    {
        Coupon::destroy($params['id']);
        return true;
    }

    /**
     * @notes 获取优惠券详情
     * @param array $params
     * @return array
     */
    public static function detail(array $params): array
    {
        $coupon = Coupon::findOrEmpty($params['id']);
        if ($coupon->isEmpty()) {
            return [];
        }
        return $coupon->toArray();
    }

    /**
     * @notes 修改状态
     * @param array $params
     * @return bool
     */
    public static function updateStatus(array $params): bool
    {
        Coupon::update([
            'id' => $params['id'],
            'status' => $params['status']
        ]);
        return true;
    }
}
