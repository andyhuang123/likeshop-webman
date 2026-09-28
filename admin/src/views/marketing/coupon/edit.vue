<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="600px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="120px" :rules="formRules">
                <el-form-item :label='$ui("优惠券名称")' prop="name">
                    <el-input v-model="formData.name" :placeholder='$ui("请输入优惠券名称")' clearable />
                </el-form-item>
                <el-form-item :label='$ui("优惠类型")' prop="type">
                    <el-radio-group v-model="formData.type">
                        <el-radio :label="1">{{ $ui("满减券") }}</el-radio>
                        <el-radio :label="2">{{ $ui("折扣券") }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label='$ui("面额")' prop="money" v-if="formData.type === 1">
                    <el-input-number v-model="formData.money" :min="0" :precision="2" />
                    <span class="ml-2">{{ $ui("元") }}</span>
                </el-form-item>
                <el-form-item :label='$ui("折扣率")' prop="discount" v-if="formData.type === 2">
                    <el-input-number v-model="formData.discount" :min="0" :max="10" :precision="1" />
                    <span class="ml-2">{{ $ui("折 (例如：8.5折)") }}</span>
                </el-form-item>
                <el-form-item :label='$ui("使用门槛")' prop="condition_type">
                    <el-radio-group v-model="formData.condition_type">
                        <el-radio :label="1">{{ $ui("无门槛") }}</el-radio>
                        <el-radio :label="2">{{ $ui("满减") }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label='$ui("门槛金额")' prop="condition_money" v-if="formData.condition_type === 2">
                    <el-input-number v-model="formData.condition_money" :min="0" :precision="2" />
                    <span class="ml-2">{{ $ui("元") }}</span>
                </el-form-item>
                <el-form-item :label='$ui("发放总量")' prop="send_total">
                    <el-input-number v-model="formData.send_total" :min="0" :precision="0" />
                    <span class="ml-2">{{ $ui("张") }}</span>
                </el-form-item>
                <el-form-item :label='$ui("每人限领")' prop="get_limit">
                    <el-input-number v-model="formData.get_limit" :min="1" :precision="0" />
                    <span class="ml-2">{{ $ui("张") }}</span>
                </el-form-item>
                <el-form-item :label='$ui("发放时间")' prop="send_time">
                    <el-date-picker
                        v-model="formData.send_time"
                        type="datetimerange"
                        :range-separator='$ui("至")'
                        :start-placeholder='$ui("开始时间")'
                        :end-placeholder='$ui("结束时间")'
                        value-format="X"
                    />
                </el-form-item>
                <el-form-item :label='$ui("用券时间类型")' prop="use_time_type">
                    <el-radio-group v-model="formData.use_time_type">
                        <el-radio :label="1">{{ $ui("固定时间") }}</el-radio>
                        <el-radio :label="2">{{ $ui("领券后有效") }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label='$ui("用券时间")' prop="use_time_range" v-if="formData.use_time_type === 1">
                    <el-date-picker
                        v-model="formData.use_time_range"
                        type="datetimerange"
                        :range-separator='$ui("至")'
                        :start-placeholder='$ui("开始时间")'
                        :end-placeholder='$ui("结束时间")'
                        value-format="X"
                    />
                </el-form-item>
                <el-form-item :label='$ui("有效天数")' prop="use_time" v-if="formData.use_time_type === 2">
                    <el-input-number v-model="formData.use_time" :min="1" :precision="0" />
                    <span class="ml-2">{{ $ui("天") }}</span>
                </el-form-item>
                <el-form-item :label='$ui("状态")' prop="status">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup>
import { translateUiText } from "@/i18n";
import { reactive, ref, shallowRef } from 'vue'
import type { FormInstance, FormRules } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { apiCouponAdd, apiCouponEdit, apiCouponDetail } from '@/api/marketing/coupon'
import feedback from '@/utils/feedback'

const emit = defineEmits(['success', 'close'])
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formRef = shallowRef<FormInstance>()
const popupTitle = ref(translateUiText("新增优惠券"))

const formData = reactive({
    id: '',
    name: '',
    type: 1,
    money: 0,
    discount: 0,
    condition_type: 1,
    condition_money: 0,
    send_total: 0,
    get_limit: 1,
    send_time: [] as any[],
    use_time_type: 1,
    use_time_range: [] as any[],
    use_time: 0,
    status: 1
})

const formRules: FormRules = {
    name: [{ required: true, message: () => translateUiText("请输入优惠券名称"), trigger: 'blur' }],
    type: [{ required: true, message: () => translateUiText("请选择优惠类型"), trigger: 'change' }],
    send_total: [{ required: true, message: () => translateUiText("请输入发放总量"), trigger: 'blur' }],
    get_limit: [{ required: true, message: () => translateUiText("请输入每人限领数量"), trigger: 'blur' }],
    send_time: [{ required: true, message: () => translateUiText("请选择发放时间"), trigger: 'change' }],
    use_time_type: [{ required: true, message: () => translateUiText("请选择用券时间类型"), trigger: 'change' }]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    const params = { ...formData }
    
    // Process time ranges
    if (params.send_time && params.send_time.length === 2) {
        params.send_time_start = params.send_time[0]
        params.send_time_end = params.send_time[1]
    }
    
    if (params.use_time_type === 1 && params.use_time_range && params.use_time_range.length === 2) {
        params.use_time_start = params.use_time_range[0]
        params.use_time_end = params.use_time_range[1]
    }
    
    // Clean up temporary fields
    delete (params as any).send_time
    delete (params as any).use_time_range
    
    if (params.id) {
        await apiCouponEdit(params)
        feedback.msgSuccess(translateUiText("编辑成功"))
    } else {
        await apiCouponAdd(params)
        feedback.msgSuccess(translateUiText("新增成功"))
    }
    
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = (type: string) => {
    popupTitle.value = type === 'add' ? translateUiText("新增优惠券") : translateUiText("编辑优惠券")
    popupRef.value?.open()
}

const setFormData = async (row: any) => {
    // Reset form first
    Object.assign(formData, {
        id: '',
        name: '',
        type: 1,
        money: 0,
        discount: 0,
        condition_type: 1,
        condition_money: 0,
        send_total: 0,
        get_limit: 1,
        send_time: [],
        use_time_type: 1,
        use_time_range: [],
        use_time: 0,
        status: 1
    })
    
    if (row) {
        const data = await apiCouponDetail({ id: row.id })
        Object.assign(formData, data)
        // Recover time ranges
        if (data.send_time_start && data.send_time_end) {
            formData.send_time = [data.send_time_start, data.send_time_end]
        }
        if (data.use_time_start && data.use_time_end) {
            formData.use_time_range = [data.use_time_start, data.use_time_end]
        }
    }
}

defineExpose({
    open,
    setFormData
})
</script>
