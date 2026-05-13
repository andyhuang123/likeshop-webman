<?php
namespace app\adminapi\validate\marketing;

use app\common\validate\BaseValidate;

class BlindBoxValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'name' => 'require|max:255',
        'price' => 'require|float|egt:0',
        'image' => 'require',
        'status' => 'require|in:0,1',
        'blind_box_detail' => 'array',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'id.integer' => '参数错误',
        'name.require' => '请输入盲盒名称',
        'name.max' => '盲盒名称不能超过255个字符',
        'price.require' => '请输入价格',
        'price.float' => '价格格式错误',
        'price.egt' => '价格不能小于0',
        'image.require' => '请上传封面图',
        'status.require' => '请选择状态',
        'status.in' => '状态值错误',
        'blind_box_detail.array' => '奖品配置格式错误',
    ];

    public function sceneAdd()
    {
        return $this->only(['name', 'price', 'image', 'status', 'blind_box_detail']);
    }

    public function sceneEdit()
    {
        return $this->only(['id', 'name', 'price', 'image', 'status', 'blind_box_detail']);
    }

    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }
    
    public function sceneStatus()
    {
        return $this->only(['id', 'status']);
    }
}
