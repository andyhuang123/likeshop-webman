<?php
namespace app\adminapi\lists\marketing;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\marketing\Coupon;
use app\common\lists\ListsSearchInterface;

class CouponLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * @notes 设置搜索条件
     * @return array
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name'],
            '=' => ['status', 'type']
        ];
    }

    /**
     * @notes 获取列表
     * @return array
     */
    public function lists(): array
    {
        $lists = Coupon::where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('create_time', 'desc')
            ->select()
            ->append(['type_desc', 'status_desc', 'send_time_desc', 'use_time_desc'])
            ->toArray();

        return $lists;
    }

    /**
     * @notes 获取数量
     * @return int
     */
    public function count(): int
    {
        return Coupon::where($this->searchWhere)->count();
    }
}
