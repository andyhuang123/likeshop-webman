<template>
    <div class="edit-blind-box">
        <el-card class="!border-none" shadow="never">
            <template #header>
                <span class="font-medium">{{ mode === 'edit' ? '编辑盲盒' : '新增盲盒' }}</span>
            </template>
            <el-form ref="formRef" :model="formData" label-width="100px" :rules="formRules">
                <el-form-item label="盲盒名称" prop="name">
                    <el-input v-model="formData.name" placeholder="请输入盲盒名称" class="w-[360px]" />
                </el-form-item>
                <el-form-item label="封面图" prop="image">
                    <upload-image v-model="formData.image" :limit="1" />
                </el-form-item>
                <el-form-item label="盲盒价格" prop="price">
                    <el-input-number v-model="formData.price" :min="0" :precision="2" :step="0.1" class="w-[360px]" />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <el-input-number v-model="formData.sort" :min="0" :max="9999" class="w-[360px]" />
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="!border-none mt-4 fixed-footer" shadow="never">
            <div class="flex justify-center">
                <el-button @click="handleBack">返回</el-button>
                <el-button type="primary" @click="handleSubmit">保存</el-button>
            </div>
        </el-card>
    </div>
</template>

<script lang="ts" setup name="BlindBoxEdit">
import type { FormInstance } from 'element-plus'
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { blindBoxAdd, blindBoxDetail, blindBoxEdit } from '@/api/marketing/blind_box'
import UploadImage from '@/components/upload-image/index.vue'
import feedback from '@/utils/feedback'

const route = useRoute()
const router = useRouter()
const formRef = ref<FormInstance>()
const mode = ref('add')

const formData = ref({
    id: '',
    name: '',
    image: '',
    price: 0,
    sort: 0,
    status: 1
})

const formRules = {
    name: [{ required: true, message: '请输入盲盒名称', trigger: 'blur' }],
    image: [{ required: true, message: '请上传封面图', trigger: 'change' }],
    price: [{ required: true, message: '请输入价格', trigger: 'blur', type: 'number', min: 0 }]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    const params = { ...formData.value }
    if (mode.value === 'edit') {
        await blindBoxEdit(params)
    } else {
        delete (params as any).id
        await blindBoxAdd(params)
    } 
    router.back()
}

const handleBack = () => {
    router.back()
}

const getDetail = async (id: number) => {
    const data = await blindBoxDetail({ id })
    formData.value = data
}

onMounted(() => {
    if (route.query.id) {
        mode.value = 'edit'
        getDetail(Number(route.query.id))
    }
})
</script>

<style scoped>
.edit-blind-box {
    min-height: calc(100vh - var(--navbar-height) - 40px);
}
</style>
