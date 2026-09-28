<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="warning"
                :title='$ui("用于管理商品的分类，只可添加到一级")'
                :closable="false"
                show-icon
            />
        </el-card>
        <el-card class="!border-none mt-4" shadow="never" v-loading="pager.loading">
            <div>
                <el-button
                    class="mb-4"
                    v-perms="['product.productCate/add']"
                    type="primary"
                    @click="handleAdd()"
                >
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    {{ $ui("新增") }}
                </el-button>
            </div>
            <el-table size="large" :data="pager.lists">
                <el-table-column :label='$ui("分类名称")' prop="name" min-width="120" />
                <el-table-column :label='$ui("商品数")' prop="product_count" min-width="120" />
                <el-table-column :label='$ui("状态")' min-width="120">
                    <template #default="{ row }">
                        <el-switch
                            v-perms="['product.productCate/updateStatus']"
                            v-model="row.is_show"
                            :active-value="1"
                            :inactive-value="0"
                            @change="changeStatus($event, row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("排序")' prop="sort" min-width="120" />
                <el-table-column :label='$ui("操作")' width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['product.productCate/edit']"
                            type="primary"
                            link
                            @click="handleEdit(row)"
                            >{{ $ui("编辑") }}</el-button
                        >
                        <el-button
                            v-perms="['product.productCate/delete']"
                            type="danger"
                            link
                            @click="handleDelete(row.id)"
                            >{{ $ui("删除") }}</el-button
                        >
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" @close="showEdit = false" />
    </div>
</template>
<script lang="ts" setup name="productCategory">
import { translateUiText } from "@/i18n";
import { productCateDelete, productCateLists, productCateStatus } from '@/api/product'
import { usePaging } from '@/hooks/usePaging'
import type { ProductCateItem } from '@/types/product'
import feedback from '@/utils/feedback'

import EditPopup from './edit.vue'

const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const showEdit = ref(false)

const { pager, getLists } = usePaging({
    fetchFun: productCateLists
})
const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}

const handleEdit = async (data: ProductCateItem) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit')
    editRef.value?.getDetail(data)
}

const handleDelete = async (id: number) => {
    await feedback.confirm(translateUiText("确定要删除？"))
    await productCateDelete({ id })
    getLists()
}

const changeStatus = async (is_show: string | number | boolean, id: number) => {
    try {
        await productCateStatus({ id, is_show: Number(is_show) })
        getLists()
    } catch (error) {
        getLists()
    }
}

getLists()
</script>
