<?php
namespace app\adminapi\controller\marketing;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\marketing\BlindBoxLists;
use app\adminapi\logic\marketing\BlindBoxLogic;
use app\adminapi\validate\marketing\BlindBoxValidate;
use support\Response;

class BlindBoxController extends BaseAdminController
{
    /**
     * @notes 获取盲盒列表
     * @return Response
     */
    public function lists(): Response
    {
        return $this->dataLists(new BlindBoxLists());
    }

    /**
     * @notes 添加盲盒
     * @return Response
     */
    public function add(): Response
    {
        $params = (new BlindBoxValidate())->post()->goCheck('add');
        $result = BlindBoxLogic::add($params);
        if ($result === true) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(BlindBoxLogic::getError());
    }

    /**
     * @notes 编辑盲盒
     * @return Response
     */
    public function edit(): Response
    {
        $params = (new BlindBoxValidate())->post()->goCheck('edit');
        $result = BlindBoxLogic::edit($params);
        if ($result === true) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(BlindBoxLogic::getError());
    }

    /**
     * @notes 删除盲盒
     * @return Response
     */
    public function delete(): Response
    {
        $params = (new BlindBoxValidate())->post()->goCheck('delete');
        BlindBoxLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 获取盲盒详情
     * @return Response
     */
    public function detail(): Response
    {
        $params = (new BlindBoxValidate())->goCheck('detail');
        $result = BlindBoxLogic::detail($params);
        return $this->data($result);
    }
    
    /**
     * @notes 设置奖品
     * @return Response
     */
    public function setPrizes(): Response
    {
        $params = $this->request->post();
        $result = BlindBoxLogic::setPrizes($params);
        if ($result === true) {
            return $this->success('设置成功', [], 1, 1);
        }
        return $this->fail(BlindBoxLogic::getError());
    }

    /**
     * @notes 修改状态
     * @return Response
     */
    public function status(): Response
    {
        $params = (new BlindBoxValidate())->post()->goCheck('status');
        BlindBoxLogic::status($params);
        return $this->success('修改成功', [], 1, 1);
    }
}
