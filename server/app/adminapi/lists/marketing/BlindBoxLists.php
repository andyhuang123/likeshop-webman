<?php
namespace app\adminapi\lists\marketing;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\marketing\BlindBox;
use app\common\lists\ListsSearchInterface;

class BlindBoxLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * @notes 设置搜索条件
     * @return array
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name'],
            '=' => ['status']
        ];
    }

    /**
     * @notes 获取列表
     * @return array
     */
    public function lists(): array
    {
        $lists = BlindBox::where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('sort', 'desc')
            ->order('id', 'desc')
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 获取数量
     * @return int
     */
    public function count(): int
    {
        return BlindBox::where($this->searchWhere)->count();
    }
}
