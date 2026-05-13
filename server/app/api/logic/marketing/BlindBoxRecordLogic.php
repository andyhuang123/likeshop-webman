<?php
namespace app\api\logic\marketing;

use app\common\logic\BaseLogic;
use app\common\model\marketing\BlindBoxRecord;
use app\common\model\order\Order;
use app\common\model\order\OrderGoods;
use app\common\model\user\UserAddress;
use app\common\model\user\User;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\AccountLogLogic;
use think\facade\Db;
use Exception;

class BlindBoxRecordLogic extends BaseLogic
{
    /**
     * @notes 提货
     */
    public static function ship(array $params)
    {
        Db::startTrans();
        try {
            $userId = $params['user_id'];
            $recordIds = $params['record_ids']; // array
            $addressId = $params['address_id'];

            // 1. Validate Address
            $address = UserAddress::where('id', $addressId)
                ->where('user_id', $userId)
                ->findOrEmpty();
            if ($address->isEmpty()) {
                throw new Exception('收货地址不存在');
            }

            // 2. Validate Records
            $records = BlindBoxRecord::where('user_id', $userId)
                ->whereIn('id', $recordIds)
                ->where('status', 0) // 盒柜中
                ->with(['product'])
                ->select();

            if ($records->isEmpty() || count($records) != count($recordIds)) {
                throw new Exception('包含无效或不可提货的记录');
            }

            // 3. Create Order
            $orderSn = generate_sn(Order::class, 'sn', 'O');
            $order = Order::create([
                'sn' => $orderSn,
                'user_id' => $userId,
                'order_type' => 2, // 盲盒兑换
                'order_status' => 1, // 待发货 (无需支付)
                'pay_status' => 1, // 已支付
                'pay_way' => 0, // 无需支付
                'pay_time' => time(),
                'order_amount' => 0,
                'total_amount' => 0, // calculate later?
                'shipping_price' => 0,
                'address_snapshot' => json_encode($address->toArray(), JSON_UNESCAPED_UNICODE),
                'user_remark' => $params['remark'] ?? '',
            ]);

            // 4. Create Order Goods & Update Records
            foreach ($records as $record) {
                OrderGoods::create([
                    'order_id' => $order->id,
                    'goods_id' => $record->product_id,
                    'goods_name' => $record->product->name,
                    'goods_image' => $record->product->main_image,
                    'goods_price' => 0, // Exchange
                    'goods_num' => 1,
                    'total_price' => 0,
                ]);

                $record->status = 1; // 已提货
                $record->exchange_order_id = $order->id;
                $record->save();
            }

            Db::commit();
            return ['order_id' => $order->id];
        } catch (Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 回收
     */
    public static function recycle(array $params)
    {
        Db::startTrans();
        try {
            $userId = $params['user_id'];
            $recordIds = $params['record_ids']; // array

            // 1. Validate Records
            $records = BlindBoxRecord::where('user_id', $userId)
                ->whereIn('id', $recordIds)
                ->where('status', 0) // 盒柜中
                ->with(['blindBox'])
                ->select();

            if ($records->isEmpty() || count($records) != count($recordIds)) {
                throw new Exception('包含无效或不可回收的记录');
            }

            $totalAmount = 0;
            // 2. Calculate Refund Amount
            // Default 20% of blind box price
            foreach ($records as $record) {
                $amount = bcmul($record->blindBox->price, '0.2', 2);
                $totalAmount = bcadd($totalAmount, $amount, 2);
                
                $record->status = 2; // 已回收
                $record->recycle_amount = $amount;
                $record->save();
            }

            // 3. Update User Balance
            if ($totalAmount > 0) {
                User::update(['user_money' => Db::raw("user_money + $totalAmount")], ['id' => $userId]);
                
                // Log Account Change
                AccountLogLogic::add(
                    $userId,
                    AccountLogEnum::UM_INC_BLIND_BOX_RECYCLE,
                    AccountLogEnum::INC,
                    $totalAmount,
                    '',
                    '盲盒回收'
                );
            }

            Db::commit();
            return ['recycle_amount' => $totalAmount];
        } catch (Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }
}
