<template>
    <div class="product-select">
        <popup
            ref="popupRef"
            :title="title"
            :async="true"
            width="900px"
            @confirm="handleConfirm"
            @close="handleClose"
        >
            <el-form :inline="true" :model="params" class="mb-4">
                <el-form-item label="商品名称">
                    <el-input v-model="params.name" placeholder="请输入商品名称" clearable />
                </el-form-item>
                <el-form-item label="商品分类">
                    <el-select
                        v-model="params.category_id"
                        placeholder="请选择商品分类"
                        clearable
                        class="w-60"
                    >
                        <el-option
                            v-for="item in cateOptions"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="getLists">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>

            <el-table
                size="large"
                :data="pager.lists"
                v-loading="pager.loading"
                highlight-current-row
                @current-change="handleCurrentChange"
                @selection-change="handleSelectionChange"
                ref="tableRef"
            >
                <el-table-column v-if="isMultiple" type="selection" width="55" />
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="封面" width="100">
                    <template #default="{ row }">
                        <el-image :src="row.main_image" class="w-[50px] h-[50px]" />
                    </template>
                </el-table-column>
                <el-table-column label="名称" prop="name" min-width="150" />
                <el-table-column label="价格" prop="price" min-width="100" />
                <el-table-column label="库存" prop="stock" min-width="100" />

                <!-- If single select, maybe just clicking row is enough, or add a button -->
                <el-table-column v-if="!isMultiple" label="操作" width="100" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="selectSingle(row)">选择</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </popup>
    </div>
</template>

<script lang="ts" setup>
import { computed, nextTick, reactive, ref } from 'vue'

import { productCateAll, productLists } from '@/api/product'
import Popup from '@/components/popup/index.vue'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const props = defineProps({
    isMultiple: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['confirm'])

const popupRef = ref<InstanceType<typeof Popup>>()
const tableRef = ref()
const title = computed(() => (props.isMultiple ? '选择商品(多选)' : '选择商品'))

const params = reactive({
    name: '',
    category_id: '',
    status: 1 // Only show active products
})

const { pager, getLists, resetParams } = usePaging({
    fetchFun: productLists,
    params
})

const cateOptions = ref<any[]>([])
const loadCateOptions = async () => {
    try {
        const res = await productCateAll()
        cateOptions.value = Array.isArray(res) ? res : []
    } catch (e) {
        cateOptions.value = []
    }
}

const selectedRows = ref<any[]>([])
const currentRow = ref<any>(null)

const open = () => {
    popupRef.value?.open()
    loadCateOptions()
    getLists()
    selectedRows.value = []
    currentRow.value = null
}

const handleClose = () => {
    // cleanup
}

const handleSelectionChange = (val: any[]) => {
    if (props.isMultiple) {
        selectedRows.value = val
    }
}

const handleCurrentChange = (val: any) => {
    if (!props.isMultiple) {
        currentRow.value = val
    }
}

const selectSingle = (row: any) => {
    emit('confirm', row)
    popupRef.value?.close()
}

const handleConfirm = () => {
    if (props.isMultiple) {
        if (selectedRows.value.length === 0) {
            feedback.msgWarning('请至少选择一项')
            return
        }
        emit('confirm', selectedRows.value)
    } else {
        if (!currentRow.value) {
            feedback.msgWarning('请选择一项')
            return
        }
        emit('confirm', currentRow.value)
    }
    popupRef.value?.close()
}

defineExpose({
    open
})
</script>
