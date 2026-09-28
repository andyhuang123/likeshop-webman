<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="84px" :rules="formRules">
                <el-form-item :label='$ui("分类名称")' prop="name">
                    <el-input v-model="formData.name" :placeholder='$ui("请输入分类名称")' clearable />
                </el-form-item>
                <el-form-item :label='$ui("排序")' prop="sort">
                    <div>
                        <el-input-number v-model="formData.sort" :min="0" :max="9999" />
                        <div class="form-tips">{{ $ui("默认为0， 数值越大越排前") }}</div>
                    </div>
                </el-form-item>
                <el-form-item :label='$ui("状态")' prop="is_show">
                    <el-switch v-model="formData.is_show" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup name="productCategoryEdit">
import { translateUiText } from "@/i18n";
import type { FormInstance } from 'element-plus'

import { productCateAdd, productCateDetail, productCateEdit } from '@/api/product'
import Popup from '@/components/popup/index.vue'
import type { ProductCateItem } from '@/types/product'

const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
const popupTitle = computed(() => {
    return mode.value == 'edit' ? translateUiText("编辑分类") : translateUiText("新增分类")
})
const formData = reactive({
    id: '',
    name: '',
    sort: 0,
    is_show: 1
})

const formRules = {
    name: [{ required: true, message: () => translateUiText("请输入分类名称"), trigger: ['blur'] }]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    const submitData = {
        ...formData,
        id: formData.id ? Number(formData.id) : undefined
    }
    mode.value == 'edit'
        ? await productCateEdit(submitData as any)
        : await productCateAdd(submitData as any)
    popupRef.value?.close()
    emit('success')
}

const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
}

const setFormData = (data: Record<string, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            // @ts-ignore
            formData[key] = data[key]
        }
    }
}

const getDetail = async (row: ProductCateItem) => {
    const data = await productCateDetail({ id: row.id })
    setFormData(data)
}

const handleClose = () => {
    emit('close')
}

defineExpose({ open, setFormData, getDetail })
</script>
