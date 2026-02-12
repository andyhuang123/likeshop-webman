<?php
namespace app\adminapi\lists\product;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\product\ProductCate;

class ProductCateLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
{
    public function setSearch(): array
    {
        return [];
    }

    public function setSortFields(): array
    {
        return ['create_time' => 'create_time', 'id' => 'id'];
    }

    public function setDefaultOrder(): array
    {
        return ['sort' => 'desc', 'id' => 'desc'];
    }

    public function lists(): array
    {
        $lists = ProductCate::where($this->searchWhere)
            ->append(['is_show_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order($this->sortOrder)
            ->append(['product_count'])
            ->select()
            ->toArray();

        return $lists;
    }

    public function count(): int
    {
        return ProductCate::where($this->searchWhere)->count();
    }

    public function extend(): array
    {
        return [];
    }
}

