<?php
namespace app\adminapi\controller\marketing;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\marketing\CouponLists;
use app\adminapi\logic\marketing\CouponLogic;
use app\adminapi\validate\marketing\CouponValidate;
use support\Response;

class CouponController extends BaseAdminController
{
    /**
     * @notes 获取优惠券列表
     * @return Response
     */
    public function lists(): Response
    {
        return $this->dataLists(new CouponLists());
    }

    /**
     * @notes 添加优惠券
     * @return Response
     */
    public function add(): Response
    {
        $params = (new CouponValidate())->post()->goCheck('add');
        $result = CouponLogic::add($params);
        if ($result === true) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(CouponLogic::getError());
    }

    /**
     * @notes 编辑优惠券
     * @return Response
     */
    public function edit(): Response
    {
        $params = (new CouponValidate())->post()->goCheck('edit');
        $result = CouponLogic::edit($params);
        if ($result === true) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(CouponLogic::getError());
    }

    /**
     * @notes 删除优惠券
     * @return Response
     */
    public function delete(): Response
    {
        $params = (new CouponValidate())->post()->goCheck('delete');
        CouponLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 获取优惠券详情
     * @return Response
     */
    public function detail(): Response
    {
        $params = (new CouponValidate())->goCheck('detail');
        $result = CouponLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 修改状态
     * @return Response
     */
    public function status(): Response
    {
        $params = (new CouponValidate())->post()->goCheck('status');
        CouponLogic::updateStatus($params);
        return $this->success('修改成功', [], 1, 1);
    }
}
