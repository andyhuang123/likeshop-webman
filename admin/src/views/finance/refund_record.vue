<template>
    <div>
        <el-card class="!border-none mb-4" shadow="never">
            <div class="flex flex-wrap">
                <div class="w-1/2 md:w-1/4">
                    <div class="leading-10">{{ $ui("累计退款金额 (元)") }}</div>
                    <div class="text-6xl">{{ refundData.total }}</div>
                </div>
                <div class="w-1/2 md:w-1/4">
                    <div class="leading-10">{{ $ui("退款中金额 (元)") }}</div>
                    <div class="text-6xl">{{ refundData.ing }}</div>
                </div>
                <div class="w-1/2 md:w-1/4">
                    <div class="leading-10">{{ $ui("退款成功金额 (元)") }}</div>
                    <div class="text-6xl">{{ refundData.success }}</div>
                </div>
                <div class="w-1/2 md:w-1/4">
                    <div class="leading-10">{{ $ui("退款失败金额 (元)") }}</div>
                    <div class="text-6xl">{{ refundData.error }}</div>
                </div>
            </div>
        </el-card>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px] mt-[16px]" :model="queryParams" :inline="true">
                <el-form-item :label='$ui("退款单号")'>
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.sn"
                        :placeholder='$ui("请输入退款单号")'
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item :label='$ui("来源单号")'>
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.order_sn"
                        :placeholder='$ui("请输入来源单号")'
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item :label='$ui("用户信息")'>
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.user_info"
                        :placeholder='$ui("请输入用户信息")'
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item :label='$ui("退款类型")'>
                    <el-select class="w-[280px]" v-model="queryParams.refund_type">
                        <el-option :label='$ui("全部")' value />
                        <el-option :label='$ui("后台退款")' :value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item :label='$ui("记录时间")'>
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">{{ $ui("查询") }}</el-button>
                    <el-button @click="resetParams">{{ $ui("重置") }}</el-button>
                    <!-- <export-data
                        class="ml-2.5"
                        :fetch-fun="refundRecord"
                        :params="queryParams"
                        :page-size="pager.size"
                    /> -->
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-tabs v-model="activeTab" @tab-change="handleTabChange">
                <el-tab-pane
                    v-for="(item, index) in tabLists"
                    :label="`${$ui(item.name)}(${pager.extend[item.numKey] ?? 0})`"
                    :name="index"
                    :key="index"
                >
                    <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                        <el-table-column :label='$ui("退款单号")' prop="sn" min-width="190" />
                        <el-table-column :label='$ui("用户信息")' min-width="160">
                            <template #default="{ row }">
                                <div class="flex items-center">
                                    <image-contain
                                        class="flex-none mr-2"
                                        :src="row.avatar"
                                        :width="40"
                                        :height="40"
                                        preview-teleported
                                        fit="contain"
                                    />
                                    {{ row.nickname }}
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column :label='$ui("来源单号")' prop="order_sn" min-width="190" />
                        <el-table-column :label='$ui("退款金额")' min-width="100">
                            <template #default="{ row }"> ¥ {{ row.refund_amount }} </template>
                        </el-table-column>

                        <el-table-column :label='$ui("退款类型")' prop="refund_type_text" min-width="100" />
                        <el-table-column :label='$ui("退款状态")' prop="" min-width="100">
                            <template #default="{ row }">
                                <el-tag type="warning" v-if="row.refund_status == 0">
                                    {{ row.refund_status_text }}
                                </el-tag>
                                <el-tag v-if="row.refund_status == 1">
                                    {{ row.refund_status_text }}
                                </el-tag>
                                <el-tag type="danger" v-if="row.refund_status == 2">
                                    {{ row.refund_status_text }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column :label='$ui("记录时间")' prop="create_time" min-width="180" />
                        <el-table-column :label='$ui("操作")' width="180" fixed="right">
                            <template #default="{ row }">
                                <el-button
                                    v-perms="['finance.refund/log']"
                                    type="primary"
                                    link
                                    @click="handleShowRefundLog(row.id)"
                                >
                                    {{ $ui("退款日志") }}
                                </el-button>
                                <el-button
                                    v-if="row.refund_status == 2"
                                    v-perms="['recharge.recharge/refundAgain']"
                                    type="primary"
                                    link
                                    @click="handleRefund(row.id)"
                                >
                                    {{ $ui("重新退款") }}
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
            </el-tabs>

            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <refund-log v-model="showRefundLog" :refund-id="selectRefundId" />
    </div>
</template>
<script lang="ts" setup name="refundRecord">
import { translateUiText } from "@/i18n";
import { refundAgain, refundRecord, refundStat } from '@/api/finance'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

import RefundLog from './component/refund-log.vue'

const queryParams = reactive({
    sn: '',
    order_sn: '',
    user_info: '',
    refund_type: '',
    start_time: '',
    end_time: '',
    refund_status: ''
})
const refundData = reactive({
    total: 0,
    ing: 0,
    success: 0,
    error: 0
})
const showRefundLog = ref(false)
const selectRefundId = ref(0)
const activeTab = ref(0)
const tabLists = ref([
    {
        name: '全部',
        type: '',
        numKey: 'total'
    },
    {
        name: '退款中',
        type: 0,
        numKey: 'ing'
    },
    {
        name: '退款成功',
        type: 1,
        numKey: 'success'
    },
    {
        name: '退款失败',
        type: 2,
        numKey: 'error'
    }
])

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: refundRecord,
    params: queryParams
})

const handleRefund = async (id: number) => {
    await feedback.confirm(translateUiText("确认重新退款？"))
    await refundAgain({
        record_id: id
    })
    getLists()
    getRefundStat()
}

const handleShowRefundLog = async (id: number) => {
    showRefundLog.value = true
    selectRefundId.value = id
}
const handleTabChange = (index: any) => {
    queryParams.refund_status = tabLists.value[index].type as string
    resetPage()
}

const getRefundStat = async () => {
    const res = await refundStat()
    Object.assign(refundData, res)
}
getRefundStat()
getLists()
</script>
