<?php
namespace app\adminapi\validate\marketing;

use app\common\validate\BaseValidate;

class CouponValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'name' => 'require|max:100',
        'type' => 'require|in:1,2',
        'money' => 'float|egt:0',
        'discount' => 'float|between:0,10',
        'condition_type' => 'require|in:1,2',
        'condition_money' => 'float|egt:0',
        'send_total' => 'require|integer|egt:0',
        'get_limit' => 'require|integer|gt:0',
        'send_time_start' => 'require|integer',
        'send_time_end' => 'require|integer', // Custom check needed for end > start
        'use_time_type' => 'require|in:1,2',
        'use_time_start' => 'integer',
        'use_time_end' => 'integer',
        'use_time' => 'integer|gt:0',
        'status' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'id.integer' => '参数错误',
        'name.require' => '请输入优惠券名称',
        'name.max' => '优惠券名称不能超过100个字符',
        'type.require' => '请选择优惠类型',
        'type.in' => '优惠类型错误',
        'money.float' => '面额格式错误',
        'money.egt' => '面额不能小于0',
        'discount.float' => '折扣格式错误',
        'discount.between' => '折扣率必须在0-10之间',
        'condition_type.require' => '请选择使用门槛',
        'condition_money.float' => '门槛金额格式错误',
        'send_total.require' => '请输入发放总量',
        'send_total.integer' => '发放总量必须为整数',
        'get_limit.require' => '请输入每人限领数量',
        'get_limit.integer' => '限领数量必须为整数',
        'send_time_start.require' => '请选择发放开始时间',
        'send_time_end.require' => '请选择发放结束时间',
        'use_time_type.require' => '请选择用券时间类型',
        'status.require' => '请选择状态',
        'status.in' => '状态值错误',
    ];

    public function sceneAdd()
    {
        return $this->only([
            'name', 'type', 'money', 'discount', 'condition_type', 'condition_money',
            'send_total', 'get_limit', 'send_time_start', 'send_time_end',
            'use_time_type', 'use_time_start', 'use_time_end', 'use_time', 'status'
        ]);
    }

    public function sceneEdit()
    {
        return $this->only([
            'id', 'name', 'type', 'money', 'discount', 'condition_type', 'condition_money',
            'send_total', 'get_limit', 'send_time_start', 'send_time_end',
            'use_time_type', 'use_time_start', 'use_time_end', 'use_time', 'status'
        ]);
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
