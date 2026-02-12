<template>
    <div class="product-edit">
        <el-card class="!border-none" shadow="never">
            <el-page-header :content="$route.meta.title || (mode == 'edit' ? '编辑商品' : '新增商品')" @back="$router.back()" />
        </el-card>
        <el-card class="mt-4 !border-none" shadow="never">
            <el-form ref="formRef" class="ls-form" :model="formData" label-width="85px" :rules="rules">
                <div class="xl:flex">
                    <div>
                        <el-form-item label="商品名称" prop="name">
                            <div class="w-80">
                                <el-input v-model="formData.name" placeholder="请输入商品名称" maxlength="64" show-word-limit clearable />
                            </div>
                        </el-form-item>
                        <el-form-item label="SKU" prop="sku">
                            <div class="w-80">
                                <el-input v-model="formData.sku" placeholder="请输入SKU" maxlength="64" clearable />
                            </div>
                        </el-form-item>
                        <el-form-item label="商品分类" prop="cid">
                            <el-select class="w-80" v-model="formData.cid" placeholder="请选择商品分类" clearable>
                                <el-option v-for="item in cateOptions" :key="item.id" :label="item.name" :value="item.id" />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="价格" prop="price">
                            <div class="w-80">
                                <el-input v-model.number="formData.price" placeholder="请输入价格" />
                            </div>
                        </el-form-item>
                        <el-form-item label="库存" prop="stock">
                            <div>
                                <el-input-number v-model="formData.stock" :min="0" />
                            </div>
                        </el-form-item>
                        <el-form-item label="商品封面" prop="image">
                            <div>
                                <material-picker v-model="formData.image" :limit="1" />
                                <div class="form-tips">建议尺寸：800*800px</div>
                            </div>
                        </el-form-item>
                        <el-form-item label="排序" prop="sort">
                            <div>
                                <el-input-number v-model="formData.sort" :min="0" :max="9999" />
                                <div class="form-tips">默认为0， 数值越大越排前</div>
                            </div>
                        </el-form-item>
                        <el-form-item label="状态" required prop="is_show">
                            <el-radio-group v-model="formData.is_show">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </div>
                    <div class="xl:ml-20">
                        <el-form-item label="商品描述" prop="desc">
                            <editor v-model="formData.desc" :height="400" :width="600" />
                        </el-form-item>
                    </div>
                </div>
            </el-form>
        </el-card>
        <footer-btns>
            <el-button type="primary" @click="handleSave">保存</el-button>
        </footer-btns>
    </div>
    
</template>
<script lang="ts" setup name="productListsEdit">
import type { FormInstance } from 'element-plus'
import { productAdd, productCateAll, productDetail, productEdit } from '@/api/product'
import useMultipleTabs from '@/hooks/useMultipleTabs'

const route = useRoute()
const router = useRouter()
const mode = ref(route.query.id ? 'edit' : 'add')
const formData = reactive({
    id: '',
    name: '',
    sku: '',
    image: '',
    cid: '',
    price: 0,
    stock: 0,
    desc: '',
    sort: 0,
    is_show: 1
})

const { removeTab } = useMultipleTabs()
const formRef = shallowRef<FormInstance>()
const rules = reactive({
    name: [{ required: true, message: '请输入商品名称', trigger: 'blur' }],
    cid: [{ required: true, message: '请选择商品分类', trigger: 'blur' }],
    price: [{ required: true, message: '请输入商品价格', trigger: 'blur' }]
})

const cateOptions = ref<any[]>([])
const loadCateOptions = async () => {
    cateOptions.value = await productCateAll()
}

const getDetails = async () => {
    const data = await productDetail({ id: route.query.id })
    Object.keys(formData).forEach((key) => {
        //@ts-ignore
        formData[key] = data[key]
    })
}

const handleSave = async () => {
    await formRef.value?.validate()
    if (route.query.id) {
        await productEdit(formData)
    } else {
        await productAdd(formData)
    }
    removeTab()
    router.back()
}

loadCateOptions()
route.query.id && getDetails()
</script>

