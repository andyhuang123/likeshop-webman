<template>
    <div class="coupon-lists">
        <el-card class="!border-none mb-4" shadow="never">
            <el-form class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="优惠券名称">
                    <el-input
                        v-model="queryParams.name"
                        class="w-[280px]"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="类型">
                    <el-select class="w-[280px]" v-model="queryParams.type">
                        <el-option label="全部" value="" />
                        <el-option label="满减券" :value="1" />
                        <el-option label="折扣券" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option label="开启" :value="1" />
                        <el-option label="关闭" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="!border-none" shadow="never">
            <div class="mb-4">
                <el-button type="primary" @click="handleAdd">新增优惠券</el-button>
            </div>

            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="优惠券名称" prop="name" min-width="150" />
                <el-table-column label="类型" prop="type_desc" min-width="100" />
                <el-table-column label="面额/折扣" min-width="100">
                    <template #default="{ row }">
                        <span v-if="row.type === 1">{{ row.money }}元</span>
                        <span v-else>{{ row.discount }}折</span>
                    </template>
                </el-table-column>
                <el-table-column label="使用门槛" min-width="100">
                    <template #default="{ row }">
                        <span v-if="row.condition_type === 1">无门槛</span>
                        <span v-else>满{{ row.condition_money }}元</span>
                    </template>
                </el-table-column>
                <el-table-column label="发放总量" prop="send_total" min-width="100" />
                <el-table-column label="剩余" min-width="100">
                    <template #default="{ row }">
                        {{ row.send_total - row.get_count }}
                    </template>
                </el-table-column>
                <el-table-column label="发放时间" prop="send_time_desc" min-width="180" show-overflow-tooltip />
                <el-table-column label="用券时间" prop="use_time_desc" min-width="180" show-overflow-tooltip />
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                            @change="handleStatus(row)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleEdit(row)"> 编辑 </el-button>
                        <el-button type="danger" link @click="handleDelete(row.id)">
                            删除
                        </el-button>
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

<script lang="ts" setup name="CouponLists">
import { reactive, ref, nextTick } from 'vue'
import { apiCouponLists, apiCouponDelete, apiCouponStatus } from '@/api/marketing/coupon'
import Pagination from '@/components/pagination/index.vue'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'

const queryParams = reactive({
    name: '',
    status: '',
    type: ''
})

const showEdit = ref(false)
const editRef = ref()

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: apiCouponLists,
    params: queryParams
})

const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
    editRef.value?.setFormData()
}

const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit')
    editRef.value?.setFormData(data)
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除此优惠券吗？')
    await apiCouponDelete({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

const handleStatus = async (row: any) => {
    try {
        await apiCouponStatus({ id: row.id, status: row.status })
        feedback.msgSuccess('修改成功')
    } catch (error) {
        row.status = row.status === 1 ? 0 : 1
    }
}

getLists()
</script>
