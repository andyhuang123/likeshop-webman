<template>
    <div>
        <el-card class="!border-none" shadow="never" v-loading="pager.loading">
            <el-form :inline="true" :model="params" class="mb-4">
                <el-form-item label="商品名称">
                    <el-input v-model="params.name" placeholder="请输入商品名称" clearable />
                </el-form-item>
                <el-form-item label="商品分类">
                    <el-select v-model="params.cid" placeholder="请选择商品分类" clearable class="w-60">
                        <el-option v-for="item in cateOptions" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select v-model="params.is_show" placeholder="请选择状态" clearable class="w-40">
                        <el-option label="显示" :value="1" />
                        <el-option label="隐藏" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="getLists">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>

            <div>
                <el-button class="mb-4" v-perms="['product.product/add']" type="primary" @click="handleAdd()">
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
            </div>

            <el-table size="large" :data="pager.lists">
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="封面" width="100">
                    <template #default="{ row }">
                        <file-item :uri="row.image_url" file-size="50px" type="image" />
                    </template>
                </el-table-column>
                <el-table-column label="名称" prop="name" min-width="150" />
                <el-table-column label="SKU" prop="sku" min-width="120" />
                <el-table-column label="价格" prop="price" min-width="120" />
                <el-table-column label="库存" prop="stock" min-width="100" />
                <el-table-column label="状态" min-width="120">
                    <template #default="{ row }">
                        <el-switch v-perms="['product.product/updateStatus']" v-model="row.is_show" :active-value="1" :inactive-value="0" @change="changeStatus($event, row.id)" />
                    </template>
                </el-table-column>
                <el-table-column label="排序" prop="sort" min-width="100" />
                <el-table-column label="创建时间" prop="create_time" min-width="160" />
                <el-table-column label="操作" width="160" fixed="right">
                    <template #default="{ row }">
                        <el-button v-perms="['product.product/edit']" type="primary" link @click="handleEdit(row)">编辑</el-button>
                        <el-button v-perms="['product.product/delete']" type="danger" link @click="handleDelete(row.id)">删除</el-button>
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
<script lang="ts" setup name="productLists">
import { productCateAll, productDelete, productLists, productStatus } from '@/api/product'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'
import FileItem from '@/views/material/file.vue'

const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const showEdit = ref(false)

const { pager, getLists, resetParams, params } = usePaging({
    fetchFun: productLists,
    params: { name: '', cid: '', is_show: '' }
})

const cateOptions = ref<any[]>([])
const loadCateOptions = async () => {
    cateOptions.value = await productCateAll()
}

const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}

const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit')
    editRef.value?.getDetail(data)
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除？')
    await productDelete({ id })
    getLists()
}

const changeStatus = async (is_show: any, id: number) => {
    try {
        await productStatus({ id, is_show })
        getLists()
    } catch (error) {
        getLists()
    }
}

loadCateOptions()
getLists()
</script>

