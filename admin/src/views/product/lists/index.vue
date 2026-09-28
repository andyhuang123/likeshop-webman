<template>
    <div>
        <el-card class="!border-none" shadow="never" v-loading="pager.loading">
            <el-form :inline="true" :model="params" class="mb-4">
                <el-form-item :label='$ui("商品名称")'>
                    <el-input v-model="params.name" :placeholder='$ui("请输入商品名称")' clearable />
                </el-form-item>
                <el-form-item :label='$ui("商品分类")'>
                    <el-select
                        v-model="params.category_id"
                        :placeholder='$ui("请选择商品分类")'
                        clearable
                        class="w-60"
                    >
                        <el-option
                            v-for="item in cateOptions"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item :label='$ui("状态")'>
                    <el-select
                        v-model="params.status"
                        :placeholder='$ui("请选择状态")'
                        clearable
                        class="w-40"
                    >
                        <el-option :label='$ui("显示")' :value="1" />
                        <el-option :label='$ui("隐藏")' :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="getLists">{{ $ui("查询") }}</el-button>
                    <el-button @click="resetParams">{{ $ui("重置") }}</el-button>
                </el-form-item>
            </el-form>

            <div>
                <el-button
                    class="mb-4"
                    v-perms="['product.product/add']"
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
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column :label='$ui("封面")' width="100">
                    <template #default="{ row }">
                        <file-item :uri="row.main_image" file-size="50px" type="image" />
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("名称")' prop="name" min-width="150" />
                <el-table-column label="SKU" prop="sku" min-width="120" />
                <el-table-column :label='$ui("价格")' min-width="120">
                    <template #default="{ row }">
                        <div>¥{{ row.price }}</div>
                        <div v-if="row.market_price" class="text-xs text-gray-400 line-through">
                            ¥{{ row.market_price }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("库存")' prop="stock" min-width="100" />
                <el-table-column :label='$ui("状态")' min-width="120">
                    <template #default="{ row }">
                        <el-switch
                            v-perms="['product.product/updateStatus']"
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                            @change="changeStatus($event, row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("排序")' prop="sort" min-width="100" />
                <el-table-column :label='$ui("创建时间")' prop="create_time" min-width="160" />
                <el-table-column :label='$ui("操作")' width="160" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['product.product/edit']"
                            type="primary"
                            link
                            @click="handleEdit(row)"
                            >{{ $ui("编辑") }}</el-button
                        >
                        <el-button
                            v-perms="['product.product/delete']"
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
    </div>
</template>
<script lang="ts" setup name="productLists">
import { translateUiText } from "@/i18n";
import { productCateAll, productDelete, productLists, productStatus } from '@/api/product'
import FileItem from '@/components/material/file.vue'
import { usePaging } from '@/hooks/usePaging'
import type { ProductItem } from '@/types/product'
import feedback from '@/utils/feedback'

const router = useRouter()

const params = reactive({
    name: '',
    category_id: '',
    status: ''
})

const { pager, getLists, resetParams } = usePaging({
    fetchFun: productLists,
    params
})

const cateOptions = ref<{ id: number; name: string }[]>([])
const loadCateOptions = async () => {
    try {
        const res = await productCateAll()
        cateOptions.value = Array.isArray(res) ? res : []
    } catch (e) {
        cateOptions.value = []
    }
}

const handleAdd = async () => {
    router.push('/product/lists/edit')
}

const handleEdit = async (data: ProductItem) => {
    router.push({
        path: '/product/lists/edit',
        query: {
            id: String(data.id)
        }
    })
}

const handleDelete = async (id: number) => {
    await feedback.confirm(translateUiText("确定要删除？"))
    await productDelete({ id })
    getLists()
}

const changeStatus = async (status: string | number | boolean, id: number) => {
    try {
        await productStatus({ id, status: Number(status) })
        getLists()
    } catch (error) {
        getLists()
    }
}

loadCateOptions()
getLists()
</script>
