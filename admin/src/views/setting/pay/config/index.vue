<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="warning"
                :title='$ui("温馨提示：设置系统支持的支付方式")'
                :closable="false"
                show-icon
            />
        </el-card>
        <el-card shadow="never" class="mt-4 !border-none">
            <div>
                <el-table :data="payConfigList">
                    <el-table-column prop="pay_way_name" :label='$ui("支付方式")' min-width="150" />
                    <el-table-column prop="name" :label='$ui("显示名称")' min-width="150" />
                    <el-table-column :label='$ui("图标")' min-width="150">
                        <template #default="{ row }">
                            <el-image
                                :src="row.icon"
                                :alt='$ui("图标")'
                                style="width: 34px; height: 34px"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column prop="sort" :label='$ui("排序")' min-width="150" />
                    <el-table-column :label='$ui("操作")' min-width="80" fixed="right">
                        <!-- 操作 -->
                        <template #default="{ row }">
                            <el-button
                                v-perms="['setting.pay.pay_config/setConfig']"
                                link
                                type="primary"
                                @click="handleEdit(row)"
                            >
                                {{ $ui("配置") }}
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </el-card>
        <edit-popup v-if="showEdit" ref="editRef" @success="getConfig" @close="showEdit = false" />
    </div>
</template>

<script lang="ts" setup>
import { getPayConfigLists } from '@/api/setting/pay'

import EditPopup from './edit.vue'

const payConfigList = ref<any[]>([])
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const showEdit = ref(false)
const getConfig = async () => {
    const { lists } = await getPayConfigLists()
    payConfigList.value = lists
}
const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open()
    editRef.value?.getDetail(data)
}
getConfig()
</script>
