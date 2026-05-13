<?php
namespace app\api\controller\marketing;

use app\api\controller\BaseApiController;
use app\api\logic\marketing\BlindBoxRecordLogic;
use app\common\model\marketing\BlindBoxRecord;

class BlindBoxRecordController extends BaseApiController
{
    /**
     * @notes 盒柜记录列表
     */
    public function lists()
    {
        $params = $this->request->get();
        $limit = $params['limit'] ?? 10;
        
        $where = [
            ['user_id', '=', $this->userId]
        ];
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', $params['status']];
        }

        $lists = BlindBoxRecord::where($where)
            ->with(['product', 'blindBox'])
            ->order('id', 'desc')
            ->paginate($limit);

        return $this->data($lists);
    }

    /**
     * @notes 提货
     */
    public function ship()
    {
        $params = $this->request->post();
        $params['user_id'] = $this->userId;
        $result = BlindBoxRecordLogic::ship($params);
        if (false === $result) {
            return $this->fail(BlindBoxRecordLogic::getError());
        }
        return $this->success('提货成功', $result);
    }

    /**
     * @notes 回收
     */
    public function recycle()
    {
        $params = $this->request->post();
        $params['user_id'] = $this->userId;
        $result = BlindBoxRecordLogic::recycle($params);
        if (false === $result) {
            return $this->fail(BlindBoxRecordLogic::getError());
        }
        return $this->success('回收成功', $result);
    }
}
