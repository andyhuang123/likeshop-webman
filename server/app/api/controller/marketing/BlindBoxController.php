<?php
namespace app\api\controller\marketing;

use app\api\controller\BaseApiController;
use app\common\model\marketing\BlindBox;
use app\common\model\marketing\BlindBoxDetail;
use app\common\service\FileService;
use support\Response;

class BlindBoxController extends BaseApiController
{
    /**
     * @notes 获取盲盒列表
     * @return Response
     */
    public function lists(): Response
    {
        $lists = BlindBox::where('status', 1)
            ->order('sort', 'desc')
            ->order('id', 'desc')
            ->select()
            ->toArray();
            
        return $this->data(['lists' => $lists]);
    }

    /**
     * @notes 获取盲盒详情
     * @return Response
     */
    public function detail(): Response
    {
        $id = $this->request->get('id');
        $blindBox = BlindBox::where('id', $id)->where('status', 1)->findOrEmpty();
        
        if ($blindBox->isEmpty()) {
            return $this->fail('盲盒不存在');
        }
        
        $blindBox = $blindBox->toArray();
        $blindBox['blind_box_detail'] = BlindBoxDetail::where('blind_box_id', $id)
            ->with(['product' => function($query){
                $query->field('id,name,image,price');
            }])
            ->select()
            ->toArray();

        // 处理图片
        foreach ($blindBox['blind_box_detail'] as &$item) {
            if (isset($item['product']['image'])) {
                $item['product']['image'] = FileService::getFileUrl($item['product']['image']);
            }
        }

        return $this->data($blindBox);
    }
}
