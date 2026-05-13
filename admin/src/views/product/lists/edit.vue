<template>
    <div class="product-edit">
        <el-card class="!border-none" shadow="never">
            <el-page-header
                :content="route.meta.title || (mode == 'edit' ? '编辑商品' : '新增商品')"
                @back="router.back()"
            />
        </el-card>
        <el-card class="mt-4 !border-none" shadow="never">
            <el-form
                ref="formRef"
                class="ls-form"
                :model="formData"
                label-width="85px"
                :rules="rules"
            >
                <div class="xl:flex">
                    <div>
                        <el-form-item label="商品名称" prop="name">
                            <div class="w-80">
                                <el-input
                                    v-model="formData.name"
                                    placeholder="请输入商品名称"
                                    maxlength="64"
                                    show-word-limit
                                    clearable
                                />
                            </div>
                        </el-form-item>
                        <el-form-item label="商品分类" prop="category_id">
                            <el-select
                                class="w-80"
                                v-model="formData.category_id"
                                placeholder="请选择商品分类"
                                clearable
                            >
                                <el-option
                                    v-for="item in cateOptions"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id"
                                />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="商品封面" prop="main_image">
                            <div>
                                <material-picker v-model="formData.main_image" :limit="1" />
                                <div class="form-tips">建议尺寸：800*800px</div>
                            </div>
                        </el-form-item>
                        <el-form-item label="商品轮播图" prop="product_images">
                            <div>
                                <material-picker v-model="formData.product_images" :limit="10" />
                                <div class="form-tips">建议尺寸：800*800px，最多上传10张</div>
                            </div>
                        </el-form-item>
                        <el-form-item label="商品规格" required>
                            <el-radio-group
                                v-model="formData.spec_type"
                                @change="handleSpecTypeChange"
                            >
                                <el-radio :label="1">单规格</el-radio>
                                <el-radio :label="2">多规格</el-radio>
                            </el-radio-group>
                        </el-form-item>

                        <!-- 单规格 -->
                        <div v-if="formData.spec_type === 1">
                            <el-form-item label="SKU编码" prop="sku">
                                <div class="w-80">
                                    <el-input
                                        v-model="formData.sku"
                                        placeholder="请输入SKU编码"
                                        maxlength="64"
                                        clearable
                                    />
                                </div>
                            </el-form-item>
                            <el-form-item label="价格" prop="price">
                                <div class="w-80">
                                    <el-input
                                        v-model.number="formData.price"
                                        placeholder="请输入价格"
                                    />
                                </div>
                            </el-form-item>
                            <el-form-item label="划线价" prop="market_price">
                                <div class="w-80">
                                    <el-input
                                        v-model.number="formData.market_price"
                                        placeholder="请输入划线价"
                                    />
                                </div>
                            </el-form-item>
                            <el-form-item label="成本价" prop="cost_price">
                                <div class="w-80">
                                    <el-input
                                        v-model.number="formData.cost_price"
                                        placeholder="请输入成本价"
                                    />
                                </div>
                            </el-form-item>
                            <el-form-item label="库存" prop="stock">
                                <div>
                                    <el-input-number v-model="formData.stock" :min="0" />
                                </div>
                            </el-form-item>
                            <el-form-item label="重量(kg)" prop="weight">
                                <div>
                                    <el-input-number v-model="formData.weight" :min="0" />
                                </div>
                            </el-form-item>
                            <el-form-item label="体积(m³)" prop="volume">
                                <div>
                                    <el-input-number v-model="formData.volume" :min="0" />
                                </div>
                            </el-form-item>
                        </div>

                        <!-- 多规格 -->
                        <div v-if="formData.spec_type === 2" class="pl-10">
                            <div class="mb-4">
                                <el-button type="primary" link @click="addSpec"
                                    >添加规格项</el-button
                                >
                            </div>
                            <div
                                v-for="(spec, index) in formData.specs"
                                :key="index"
                                class="bg-gray-50 p-4 mb-4 rounded"
                            >
                                <div class="flex items-center mb-2">
                                    <span class="mr-2">规格名：</span>
                                    <el-input
                                        v-model="spec.name"
                                        class="w-40 mr-4"
                                        placeholder="例如：颜色"
                                    />
                                    <el-button type="danger" link @click="removeSpec(index)"
                                        >删除</el-button
                                    >
                                </div>
                                <div class="flex items-center flex-wrap">
                                    <span class="mr-2">规格值：</span>
                                    <div
                                        v-for="(val, vIndex) in spec.values"
                                        :key="vIndex"
                                        class="mr-2 mb-2 relative group"
                                    >
                                        <el-tag closable @close="removeSpecValue(index, vIndex)">{{
                                            val
                                        }}</el-tag>
                                    </div>
                                    <div class="w-32">
                                        <el-input
                                            v-model="spec.tempValue"
                                            placeholder="输入后回车"
                                            @keyup.enter="addSpecValue(index)"
                                            @blur="addSpecValue(index)"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div v-if="formData.skus.length > 0" class="mt-4">
                                <el-table :data="formData.skus" border size="small">
                                    <el-table-column
                                        v-for="(spec, index) in formData.specs"
                                        :key="index"
                                        :label="spec.name"
                                    >
                                        <template #default="{ row }">
                                            {{ row.value_names.split(',')[index] }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="价格" min-width="100">
                                        <template #default="{ row }">
                                            <el-input-number
                                                v-model="row.price"
                                                :min="0"
                                                :controls="false"
                                                class="w-full"
                                            />
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="划线价" min-width="100">
                                        <template #default="{ row }">
                                            <el-input-number
                                                v-model="row.market_price"
                                                :min="0"
                                                :controls="false"
                                                class="w-full"
                                            />
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="成本价" min-width="100">
                                        <template #default="{ row }">
                                            <el-input-number
                                                v-model="row.cost_price"
                                                :min="0"
                                                :controls="false"
                                                class="w-full"
                                            />
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="库存" min-width="100">
                                        <template #default="{ row }">
                                            <el-input-number
                                                v-model="row.stock"
                                                :min="0"
                                                :controls="false"
                                                class="w-full"
                                            />
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="重量(kg)" min-width="100">
                                        <template #default="{ row }">
                                            <el-input-number
                                                v-model="row.weight"
                                                :min="0"
                                                :controls="false"
                                                class="w-full"
                                            />
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="体积(m³)" min-width="100">
                                        <template #default="{ row }">
                                            <el-input-number
                                                v-model="row.volume"
                                                :min="0"
                                                :controls="false"
                                                class="w-full"
                                            />
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="SKU编码" min-width="120">
                                        <template #default="{ row }">
                                            <el-input v-model="row.sku_code" />
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="图片" width="80">
                                        <template #default="{ row }">
                                            <material-picker
                                                v-model="row.image"
                                                :limit="1"
                                                size="small"
                                            />
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </div>

                        <el-form-item label="排序" prop="sort" class="mt-4">
                            <div>
                                <el-input-number v-model="formData.sort" :min="0" :max="9999" />
                                <div class="form-tips">默认为0， 数值越大越排前</div>
                            </div>
                        </el-form-item>
                        <el-form-item label="状态" required prop="status">
                            <el-radio-group v-model="formData.status">
                                <el-radio :label="1">显示</el-radio>
                                <el-radio :label="0">隐藏</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </div>
                    <div class="xl:ml-20">
                        <el-form-item label="商品描述" prop="description">
                            <editor v-model="formData.description" :height="400" :width="600" />
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
import { useRoute, useRouter } from 'vue-router'

import { productAdd, productCateAll, productDetail, productEdit } from '@/api/product'
import useMultipleTabs from '@/hooks/useMultipleTabs'
import type { ProductFormData, ProductSku, ProductSpec } from '@/types/product'
import feedback from '@/utils/feedback'

const route = useRoute()
const router = useRouter()
const mode = ref(route.query.id ? 'edit' : 'add')
const formData = reactive<ProductFormData>({
    id: '',
    name: '',
    sku: '',
    main_image: '',
    product_images: [],
    category_id: '',
    price: 0,
    market_price: 0,
    cost_price: 0,
    stock: 0,
    weight: 0,
    volume: 0,
    description: '',
    sort: 0,
    status: 1,
    spec_type: 1,
    specs: [],
    skus: []
})

const { removeTab } = useMultipleTabs()
const formRef = shallowRef<FormInstance>()
const rules = reactive({
    name: [{ required: true, message: '请输入商品名称', trigger: 'blur' }],
    category_id: [{ required: true, message: '请选择商品分类', trigger: 'blur' }],
    price: [{ required: true, message: '请输入商品价格', trigger: 'blur' }]
})

const cateOptions = ref<{ id: number; name: string }[]>([])
const loadCateOptions = async () => {
    cateOptions.value = await productCateAll()
}

const getDetails = async () => {
    const id = Number(route.query.id)
    if (!id) return

    const data: any = await productDetail({ id })
    if (!data || !data.id) return

    // 手动映射需要的数据字段
    formData.id = String(data.id)
    formData.name = data.name || ''
    formData.sku = data.sku || ''
    formData.main_image = data.main_image || ''
    formData.product_images = data.product_images || []
    formData.category_id = data.category_id || ''
    formData.price = data.price || 0
    formData.market_price = data.market_price || 0
    formData.cost_price = data.cost_price || 0
    formData.stock = data.stock || 0
    formData.weight = data.weight || 0
    formData.volume = data.volume || 0
    formData.description = data.description || ''
    formData.sort = data.sort || 0
    formData.status = data.status || 1
    formData.spec_type = data.spec_type || 1

    // 处理 category_id 类型
    if (formData.category_id) {
        formData.category_id = Number(formData.category_id)
    }

    // 映射规格和 SKU 数据
    if (data.specs && data.specs.length > 0) {
        formData.specs = data.specs.map((spec: any) => ({
            name: spec.name || '',
            values: spec.values || [],
            tempValue: ''
        }))
    }

    if (data.skus && data.skus.length > 0) {
        formData.skus = data.skus.map((sku: any) => ({
            value_names: sku.value_names || '',
            price: sku.price || 0,
            market_price: sku.market_price || 0,
            cost_price: sku.cost_price || 0,
            stock: sku.stock || 0,
            weight: sku.weight || 0,
            volume: sku.volume || 0,
            sku_code: sku.sku_code || '',
            image: sku.image || ''
        }))
    }
}

// 规格相关方法
const addSpec = () => {
    formData.specs.push({ name: '', values: [], tempValue: '' })
}
const removeSpec = (index: number) => {
    formData.specs.splice(index, 1)
    generateSkus()
}
const addSpecValue = (index: number) => {
    const spec = formData.specs[index]
    if (spec.tempValue && !spec.values.includes(spec.tempValue)) {
        spec.values.push(spec.tempValue)
        spec.tempValue = ''
        generateSkus()
    }
}
const removeSpecValue = (index: number, vIndex: number) => {
    formData.specs[index].values.splice(vIndex, 1)
    generateSkus()
}

// 生成SKU列表 (笛卡尔积)
const generateSkus = () => {
    if (formData.specs.length === 0) {
        formData.skus = []
        return
    }

    const specs = formData.specs.filter((s) => s.values.length > 0)
    if (specs.length === 0) {
        formData.skus = []
        return
    }

    const cartesianProduct = (arr: any[]) => {
        return arr.reduce(
            (a, b) => {
                return a
                    .map((x: any) => b.map((y: any) => x.concat([y])))
                    .reduce((c: any, d: any) => c.concat(d), [])
            },
            [[]]
        )
    }

    const valuesList = specs.map((s) => s.values)
    const combinations = cartesianProduct(valuesList)

    // 保留旧SKU数据
    const oldSkusMap = new Map()
    formData.skus.forEach((sku: any) => {
        oldSkusMap.set(sku.value_names, sku)
    })

    const newSkus = combinations.map((combo: any) => {
        const valueNames = combo.join(',')
        const oldSku = oldSkusMap.get(valueNames)
        return (
            oldSku || {
                value_names: valueNames,
                price: 0,
                market_price: 0,
                cost_price: 0,
                stock: 0,
                weight: 0,
                volume: 0,
                sku_code: '',
                image: ''
            }
        )
    })

    formData.skus = newSkus
}

const handleSpecTypeChange = () => {
    if (formData.spec_type === 1) {
        formData.specs = []
        formData.skus = []
    }
}

const handleSave = async () => {
    await formRef.value?.validate()

    // 构建提交数据
    const submitData = { ...formData }

    // 简单校验
    if (formData.spec_type === 2) {
        // 检查规格是否完善
        const hasEmptySpec = formData.specs.some((s) => !s.name || s.values.length === 0)
        if (hasEmptySpec || formData.skus.length === 0) {
            feedback.msgError('请完善规格信息')
            return
        }

        // 检查每个 SKU 价格和库存
        const hasEmptySku = formData.skus.some((sku) => !sku.price && sku.price !== 0)
        if (hasEmptySku) {
            feedback.msgError('请完善 SKU 价格')
            return
        }

        // 计算最低价格和总库存更新到主表字段
        let minPrice = formData.skus[0].price
        let totalStock = 0
        formData.skus.forEach((sku: ProductSku) => {
            if (sku.price < minPrice) minPrice = sku.price
            totalStock += sku.stock
        })
        submitData.price = minPrice
        submitData.stock = totalStock
    }

    if (route.query.id) {
        await productEdit(submitData as any)
    } else {
        await productAdd(submitData as any)
    }
    removeTab()
    router.back()
}

loadCateOptions()
route.query.id && getDetails()
</script>
<style scoped>
.product-edit :deep(.el-input-number .el-input__inner) {
    text-align: left;
}
</style>
