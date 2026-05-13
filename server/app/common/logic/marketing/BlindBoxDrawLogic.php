<?php
namespace app\common\logic\marketing;

use app\common\model\marketing\BlindBoxDetail;
use app\common\model\marketing\BlindBoxRecord;
use Exception;

class BlindBoxDrawLogic
{
    /**
     * @notes 抽奖并记录
     * @param $order
     * @return int
     * @throws Exception
     */
    public static function drawAndRecord($order)
    {
        $productId = self::draw($order->blind_box_id);

        // Create Record
        BlindBoxRecord::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'blind_box_id' => $order->blind_box_id,
            'product_id' => $productId,
            'status' => 0, // 盒柜中
        ]);

        return $productId;
    }

    /**
     * @notes 抽奖算法
     * @param int $blindBoxId
     * @return int 商品ID
     * @throws Exception
     */
    public static function draw(int $blindBoxId): int
    {
        $details = BlindBoxDetail::where('blind_box_id', $blindBoxId)
            ->column('probability', 'product_id');

        if (empty($details)) {
            throw new Exception('盲盒内无奖品');
        }

        // 过滤掉概率为0的
        $candidates = array_filter($details, function($weight) {
            return $weight > 0;
        });

        if (empty($candidates)) {
             throw new Exception('盲盒内有效奖品为空');
        }

        $totalWeight = array_sum($candidates);
        $rand = mt_rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($candidates as $productId => $weight) {
            $currentWeight += $weight;
            if ($rand <= $currentWeight) {
                return $productId;
            }
        }

        // 理论上不会执行到这里，默认返回第一个
        return array_key_first($candidates);
    }
}
