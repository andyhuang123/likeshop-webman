<?php
namespace app\api\logic\marketing;

use app\common\logic\BaseLogic;
use app\common\model\marketing\BlindBox;
use app\common\model\marketing\BlindBoxOrder;
use app\common\service\ConfigService;
use Exception;
use think\facade\Db;

class BlindBoxOrderLogic extends BaseLogic
{
    /**
     * @notes 创建盲盒订单
     * @param array $params
     * @return array|false
     */
    public static function createOrder(array $params)
    {
        Db::startTrans();
        try {
            $userId = $params['user_id'];
            $blindBoxId = $params['blind_box_id'];
            
            $blindBox = BlindBox::findOrEmpty($blindBoxId);
            if ($blindBox->isEmpty() || $blindBox->status != 1) {
                throw new Exception('盲盒不存在或已下架');
            }

            // 生成订单
            $order = BlindBoxOrder::create([
                'sn' => generate_sn(BlindBoxOrder::class, 'sn', 'BB'),
                'user_id' => $userId,
                'blind_box_id' => $blindBoxId,
                'order_amount' => $blindBox->price,
                'pay_status' => 0,
                'create_time' => time(),
            ]);

            Db::commit();
            return [
                'order_id' => $order->id,
                'from' => 'blind_box'
            ];
        } catch (Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }
}
