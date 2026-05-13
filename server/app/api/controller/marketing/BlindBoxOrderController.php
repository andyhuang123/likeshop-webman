<?php
namespace app\api\controller\marketing;

use app\api\controller\BaseApiController;
use app\api\logic\marketing\BlindBoxOrderLogic;
use app\common\model\marketing\BlindBoxOrder;
use app\common\service\FileService;
use support\Response;

class BlindBoxOrderController extends BaseApiController
{
    public array $notNeedLogin = [];

    /**
     * @notes 创建盲盒订单
     * @return Response
     */
    public function create(): Response
    {
        $params = $this->request->post();
        $params['user_id'] = $this->userId;
        $result = BlindBoxOrderLogic::createOrder($params);
        if ($result === false) {
            return $this->fail(BlindBoxOrderLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * @notes 盲盒订单详情(查询抽奖结果)
     * @return Response
     */
    public function detail(): Response
    {
        $id = $this->request->get('id');
        $order = BlindBoxOrder::where('id', $id)
            ->where('user_id', $this->userId)
            ->with(['winProduct' => function($query){
                $query->field('id,name,image,price');
            }])
            ->findOrEmpty();

        if ($order->isEmpty()) {
            return $this->fail('订单不存在');
        }

        $order = $order->toArray();
        if (isset($order['win_product']['image'])) {
            $order['win_product']['image'] = FileService::getFileUrl($order['win_product']['image']);
        }

        return $this->data($order);
    }
}
