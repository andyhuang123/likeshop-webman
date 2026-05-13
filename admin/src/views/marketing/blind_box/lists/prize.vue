<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            title="奖品配置"
            :async="true"
            width="900px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="84px">
                <div class="mb-4">
                    <el-button type="primary" @click="addProduct">
                        + 添加奖品
                    </el-button>
                </div>
                <el-table :data="formData.blind_box_detail" border style="width: 100%">
                    <el-table-column label="商品" min-width="200">
                        <template #default="{ row, $index }">
                            <div v-if="row.product_id" class="flex items-center">
                                <el-image :src="row.image" class="w-[40px] h-[40px] mr-2" />
                                <div class="truncate">{{ row.name }}</div>
                            </div>
                            <el-button
                                v-else
                                type="primary"
                                link
                                @click="selectProduct($index)"
                            >
                                选择商品
                            </el-button>
                        </template>
                    </el-table-column>
                    <el-table-column label="中奖权重" width="150">
                        <template #default="{ row }">
                            <el-input-number
                                v-model="row.probability"
                                :min="0"
                                :precision="0"
                            />
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" width="80">
                        <template #default="{ $index }">
                            <el-button type="danger" link @click="removeProduct($index)"
                                >删除</el-button
                            >
                        </template>
                    </el-table-column>
                </el-table>
            </el-form>
        </popup>

        <product-select
            ref="productSelectRef"
            :is-multiple="false"
            @confirm="handleSelectProduct"
        />
    </div>
</template>

<script lang="ts" setup name="BlindBoxPrize">
import type { FormInstance } from 'element-plus'
import { ref } from 'vue'

import { blindBoxDetail, blindBoxSetPrizes } from '@/api/marketing/blind_box'
import Popup from '@/components/popup/index.vue'
import ProductSelect from '@/components/product-select/index.vue'
import feedback from '@/utils/feedback'

const emit = defineEmits(['success', 'close'])
const formRef = ref<FormInstance>()
const popupRef = ref<InstanceType<typeof Popup>>()
const productSelectRef = ref()
const currentIndex = ref(-1)

const formData = ref({
    id: '',
    blind_box_detail: [] as any[]
})

const handleSubmit = async () => {
    if (!formData.value.blind_box_detail?.length) {
        feedback.msgError('请至少配置一个奖品')
        return
    }
    for (const item of formData.value.blind_box_detail) {
        if (!item.product_id) {
            feedback.msgError('请选择奖品商品')
            return
        }
    }
    await blindBoxSetPrizes(formData.value)
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = () => {
    popupRef.value?.open()
}

const getDetail = async (row: any) => {
    formData.value.id = row.id
    const data = await blindBoxDetail({ id: row.id })
    // 处理奖品数据格式
    if (data.blind_box_detail) {
        formData.value.blind_box_detail = data.blind_box_detail.map((item: any) => ({
            product_id: item.product_id,
            probability: item.probability,
            name: item.product?.name,
            image: item.product?.main_image
        }))
    } else {
        formData.value.blind_box_detail = []
    }
}

const addProduct = () => {
    formData.value.blind_box_detail.push({
        product_id: 0,
        probability: 0,
        name: '',
        image: ''
    })
}

const removeProduct = (index: number) => {
    formData.value.blind_box_detail.splice(index, 1)
}

const selectProduct = (index: number) => {
    currentIndex.value = index
    productSelectRef.value?.open()
}

const handleSelectProduct = (row: any) => {
    const item = formData.value.blind_box_detail[currentIndex.value]
    item.product_id = row.id
    item.name = row.name
    item.image = row.main_image
}

defineExpose({
    open,
    getDetail
})
</script>
