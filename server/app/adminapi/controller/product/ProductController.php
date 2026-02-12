<?php
namespace app\adminapi\controller\product;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\product\ProductLists;
use app\adminapi\logic\product\ProductLogic;
use app\adminapi\validate\product\ProductValidate;
use support\Response;

class ProductController extends BaseAdminController
{
    private ProductValidate $validateObj;

    public function initialize(): void
    {
        parent::initialize();
        $this->validateObj = new ProductValidate();
    }

    public function lists(): Response
    {
        return $this->dataLists(new ProductLists());
    }

    public function add(): Response
    {
        $params = $this->validateObj->post()->goCheck('add');
        ProductLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    public function edit(): Response
    {
        $params = $this->validateObj->post()->goCheck('edit');
        $result = ProductLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ProductLogic::getError());
    }

    public function delete(): Response
    {
        $params = $this->validateObj->post()->goCheck('delete');
        ProductLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    public function detail(): Response
    {
        $params = $this->validateObj->goCheck('detail');
        $result = ProductLogic::detail($params);
        return $this->data($result);
    }

    public function updateStatus(): Response
    {
        $params = $this->validateObj->post()->goCheck('status');
        $result = ProductLogic::updateStatus($params);
        if (true === $result) {
            return $this->success('修改成功', [], 1, 1);
        }
        return $this->fail(ProductLogic::getError());
    }
}

