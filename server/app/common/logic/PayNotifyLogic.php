<?php

namespace app\common\logic;

use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\recharge\RechargeOrder;
use app\common\model\marketing\BlindBoxOrder;
use app\common\logic\marketing\BlindBoxDrawLogic;
use app\common\model\user\User;
use Exception;
use think\facade\Db;
use support\Log;

/**
 * 支付成功后处理订单状态
 * Class PayNotifyLogic
 * @package app\api\logic
 */
class PayNotifyLogic extends BaseLogic
{

    public static function handle($action, $orderSn, $extra = []): true|string
    {
        Db::startTrans();
        try {
            self::$action($orderSn, $extra);
            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            Log::error(implode('-', [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ]));
            self::setError($e->getMessage());
            return $e->getMessage();
        }
    }


    /**
     * @notes 充值回调
     * @param $orderSn
     * @param array $extra
     * @author bingo
     * @date 2023/2/27 15:28
     */
    public static function recharge($orderSn, $extra = []): void
    {
        $order = RechargeOrder::where('sn', $orderSn)->findOrEmpty();
        // 增加用户累计充值金额及用户余额
        $user = User::findOrEmpty($order->user_id);
        $user->total_recharge_amount += $order->order_amount;
        $user->user_money += $order->order_amount;
        $user->save();

        // 记录账户流水
        AccountLogLogic::add(
            $order->user_id,
            AccountLogEnum::UM_INC_RECHARGE,
            AccountLogEnum::INC,
            $order->order_amount,
            $order->sn,
            '用户充值'
        );

        // 更新充值订单状态
        $order->transaction_id = $extra['transaction_id'] ?? '';
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();
    }

    /**
     * @notes 盲盒支付回调
     * @param $orderSn
     * @param array $extra
     */
    public static function blind_box($orderSn, $extra = []): void
    {
        $order = BlindBoxOrder::where('sn', $orderSn)->findOrEmpty();
        if ($order->isEmpty()) {
            // 尝试用 pay_sn 查找
            $order = BlindBoxOrder::where('pay_sn', $orderSn)->findOrEmpty();
        }
        
        if ($order->isEmpty()) {
            throw new Exception('订单不存在');
        }

        if ($order->pay_status == PayEnum::ISPAID) {
            return;
        }

        // 执行抽奖
        try {
            $winProductId = BlindBoxDrawLogic::drawAndRecord($order);
            $order->win_product_id = $winProductId;
        } catch (Exception $e) {
            Log::error("Blind Box Draw Failed Order:{$order->id} Error:" . $e->getMessage());
        }

        // 更新订单
        $order->transaction_id = $extra['transaction_id'] ?? '';
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();
    }
}
