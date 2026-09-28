<template>
    <div class="blind-box-lists">
        <el-card class="!border-none mb-4" shadow="never">
            <el-form class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item :label='$ui("盲盒名称")'>
                    <el-input
                        v-model="queryParams.name"
                        class="w-[280px]"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item :label='$ui("状态")'>
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option :label='$ui("全部")' value="" />
                        <el-option :label='$ui("开启")' :value="1" />
                        <el-option :label='$ui("关闭")' :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">{{ $ui("查询") }}</el-button>
                    <el-button @click="resetParams">{{ $ui("重置") }}</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="!border-none" shadow="never">
            <div class="mb-4">
                <el-button type="primary" @click="handleAdd">{{ $ui("新增盲盒") }}</el-button>
            </div>

            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column :label='$ui("盲盒名称")' prop="name" min-width="150" />
                <el-table-column :label='$ui("封面图")' min-width="100">
                    <template #default="{ row }">
                        <image-contain
                            v-if="row.image"
                            :src="row.image"
                            :width="60"
                            :height="60"
                            :preview-src-list="[row.image]"
                            preview-teleported
                            fit="contain"
                        />
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("价格")' prop="price" min-width="100" />
                <el-table-column :label='$ui("状态")' min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                            @change="handleStatus(row)"
                        />
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("排序")' prop="sort" min-width="80" />
                <el-table-column :label='$ui("创建时间")' prop="create_time" min-width="180" />
                <el-table-column :label='$ui("操作")' width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleEdit(row)"> {{ $ui("编辑") }} </el-button>
                        <el-button type="primary" link @click="handlePrize(row)"> {{ $ui("奖品配置") }} </el-button>
                        <el-button type="danger" link @click="handleDelete(row.id)">
                            {{ $ui("删除") }}
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <prize-popup v-if="showPrize" ref="prizeRef" @success="getLists" @close="showPrize = false" />
    </div>
</template>

<script lang="ts" setup name="BlindBoxLists">
import { translateUiText } from "@/i18n";
import { reactive, ref, nextTick } from 'vue'
import { useRouter } from 'vue-router'

import { blindBoxDelete, blindBoxLists, blindBoxStatus } from '@/api/marketing/blind_box'
import ImageContain from '@/components/image-contain/index.vue'
import Pagination from '@/components/pagination/index.vue'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

import PrizePopup from './prize.vue'

const router = useRouter()
const queryParams = reactive({
    name: '',
    status: ''
})

const showPrize = ref(false)
const prizeRef = ref()

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: blindBoxLists,
    params: queryParams
})

const handleAdd = async () => {
    router.push('/marketing/blind_box/lists/edit')
}

const handleEdit = async (data: any) => {
    router.push({
        path: '/marketing/blind_box/lists/edit',
        query: {
            id: data.id
        }
    })
}

const handlePrize = async (data: any) => {
    showPrize.value = true
    await nextTick()
    prizeRef.value?.open()
    prizeRef.value?.getDetail(data)
}

const handleDelete = async (id: number) => {
    await feedback.confirm(translateUiText("确定要删除此盲盒吗？"))
    await blindBoxDelete({ id })
    feedback.msgSuccess(translateUiText("删除成功"))
    getLists()
}

const handleStatus = async (row: any) => {
    try {
        await blindBoxStatus({ id: row.id, status: row.status })
        feedback.msgSuccess(translateUiText("修改成功"))
    } catch (error) {
        row.status = row.status === 1 ? 0 : 1
    }
}

getLists()
</script>
