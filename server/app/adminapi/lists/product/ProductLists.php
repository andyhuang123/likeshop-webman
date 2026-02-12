<?php
namespace app\adminapi\lists\product;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\product\Product;

class ProductLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
{
    public function setSearch(): array
    {
        return [
            '=' => ['cid', 'is_show'],
            '%like%' => ['name'],
        ];
    }

    public function setSortFields(): array
    {
        return ['create_time' => 'create_time', 'id' => 'id', 'sort' => 'sort'];
    }

    public function setDefaultOrder(): array
    {
        return ['sort' => 'desc', 'id' => 'desc'];
    }

    public function lists(): array
    {
        $lists = Product::where($this->searchWhere)
            ->append(['is_show_desc', 'image_url'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order($this->sortOrder)
            ->select()
            ->toArray();

        return $lists;
    }

    public function count(): int
    {
        return Product::where($this->searchWhere)->count();
    }

    public function extend(): array
    {
        return [];
    }
}

