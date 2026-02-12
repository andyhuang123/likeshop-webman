<?php
namespace app\adminapi\controller\product;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\product\ProductCateLists;
use app\adminapi\logic\product\ProductCateLogic;
use app\adminapi\validate\product\ProductCateValidate;
use support\Response;

class ProductCateController extends BaseAdminController
{
    private ProductCateValidate $validateObj;

    public function initialize(): void
    {
        parent::initialize();
        $this->validateObj = new ProductCateValidate();
    }

    public function lists(): Response
    {
        return $this->dataLists(new ProductCateLists());
    }

    public function add(): Response
    {
        $params = $this->validateObj->post()->goCheck('add');
        ProductCateLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    public function edit(): Response
    {
        $params = $this->validateObj->post()->goCheck('edit');
        $result = ProductCateLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ProductCateLogic::getError());
    }

    public function delete(): Response
    {
        $params = $this->validateObj->post()->goCheck('delete');
        ProductCateLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    public function detail(): Response
    {
        $params = $this->validateObj->goCheck('detail');
        $result = ProductCateLogic::detail($params);
        return $this->data($result);
    }

    public function updateStatus(): Response
    {
        $params = $this->validateObj->post()->goCheck('status');
        $result = ProductCateLogic::updateStatus($params);
        if (true === $result) {
            return $this->success('修改成功', [], 1, 1);
        }
        return $this->fail(ProductCateLogic::getError());
    }

    public function all(): Response
    {
        $result = ProductCateLogic::getAllData();
        return $this->data($result);
    }
}

