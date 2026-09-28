<template>
    <div class="article-edit">
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="$ui($route.meta.title)" @back="$router.back()" />
        </el-card>
        <el-card class="mt-4 !border-none" shadow="never">
            <el-form
                ref="formRef"
                class="ls-form"
                :model="formData"
                label-width="85px"
                :rules="rules"
            >
                <el-form-item :label='$ui("名称")' prop="name">
                    <div class="w-80">
                        <el-input
                            v-model="formData.name"
                            :placeholder='$ui("请输入名称")'
                            maxlength="30"
                            clearable
                        />
                    </div>
                </el-form-item>
                <el-form-item :label='$ui("类型")' prop="type">
                    <el-radio-group v-model="formData.type">
                        <el-radio :label="1">{{ $ui("定时任务") }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label='$ui("命令")' prop="command">
                    <div class="w-80">
                        <el-input
                            v-model="formData.command"
                            :placeholder='$ui("请输入thinkphp命令，如vresion")'
                            clearable
                        />
                    </div>
                </el-form-item>
                <el-form-item :label='$ui("参数")' prop="params">
                    <div class="w-80">
                        <el-input
                            v-model="formData.params"
                            :placeholder='$ui("请输入参数，例:--id 8 --name 测试")'
                            clearable
                        />
                    </div>
                </el-form-item>
                <el-form-item :label='$ui("状态")'>
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="2" />
                </el-form-item>
                <el-form-item :label='$ui("规则")' prop="expression">
                    <div class="w-80">
                        <el-input
                            @blur="getExpression"
                            v-model="formData.expression"
                            :placeholder='$ui("请输入crontab规则，例：5 9 * * *")'
                        />
                    </div>
                </el-form-item>
                <el-form-item>
                    <el-table :data="expressionLists" style="max-width: 320px">
                        <el-table-column prop="time" :label='$ui("序号")' min-width="80" />
                        <el-table-column prop="date" :label='$ui("执行时间")' min-width="240" />
                    </el-table>
                </el-form-item>
                <el-form-item :label='$ui("备注")' prop="remark">
                    <div class="w-80">
                        <el-input
                            v-model="formData.remark"
                            type="textarea"
                            :autosize="{ minRows: 3, maxRows: 6 }"
                            :maxlength="200"
                            show-word-limit
                            clearable
                        />
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
        <footer-btns>
            <el-button type="primary" @click="handleSave">{{ $ui("保存") }}</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="scheduledTaskEdit">
import { translateUiText } from "@/i18n";
import type { FormInstance } from 'element-plus'

import { crontabAdd, crontabDetail, crontabEdit, crontabExpression } from '@/api/setting/system'
import useMultipleTabs from '@/hooks/useMultipleTabs'

const route = useRoute()
const router = useRouter()
const formData = reactive({
    id: '',
    name: '',
    command: '',
    expression: '',
    params: '',
    remark: '',
    status: 1,
    type: 1
})

const { removeTab } = useMultipleTabs()
const formRef = shallowRef<FormInstance>()
const rules = reactive({
    name: [{ required: true, message: () => translateUiText("请输入名称") }],
    command: [{ required: true, message: () => translateUiText("请输入thankphp命令，如vresion") }],
    expression: [{ required: true, message: () => translateUiText("请输入crontab规则，例：5 9 * * *") }]
})

const getDetails = async () => {
    const data = await crontabDetail({
        id: route.query.id
    })
    Object.keys(formData).forEach((key) => {
        //@ts-ignore
        formData[key] = data[key]
    })
}

const expressionLists = ref([])
const getExpression = async () => {
    await formRef.value?.validateField(['expression'])
    const res = await crontabExpression({
        expression: formData.expression
    })
    expressionLists.value = res
}

const handleSave = async () => {
    await formRef.value?.validate()
    if (route.query.id) {
        await crontabEdit(formData)
    } else {
        await crontabAdd(formData)
    }
    removeTab()
    router.back()
}
onMounted(async () => {
    if (!route.query.id) {
        return
    }
    await getDetails()
    await getExpression()
})
</script>
