<template>
    <div class="menu-lists">
        <el-card class="!border-none" shadow="never">
            <div>
                <el-button v-perms="['auth.menu/add']" type="primary" @click="handleAdd()">
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    {{ $ui("新增") }}
                </el-button>
                <el-button @click="handleExpand"> {{ $ui("展开/折叠") }} </el-button>
            </div>
            <el-table
                ref="tableRef"
                class="mt-4"
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                row-key="id"
                :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
            >
                <el-table-column
                    :label='$ui("菜单名称")'
                    prop="name"
                    min-width="150"
                    show-overflow-tooltip
                />
                <el-table-column :label='$ui("类型")' prop="type" min-width="80">
                    <template #default="{ row }">
                        <div v-if="row.type == MenuEnum.CATALOGUE">{{ $ui("目录") }}</div>
                        <div v-else-if="row.type == MenuEnum.MENU">{{ $ui("菜单") }}</div>
                        <div v-else-if="row.type == MenuEnum.BUTTON">{{ $ui("按钮") }}</div>
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("图标")' prop="icon" min-width="80">
                    <template #default="{ row }">
                        <div class="flex">
                            <icon :name="row.icon" :size="20" />
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    :label='$ui("权限标识")'
                    prop="perms"
                    min-width="150"
                    show-overflow-tooltip
                />
                <el-table-column :label='$ui("状态")' prop="is_disable" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_disable == 0">{{ $ui("正常") }}</el-tag>
                        <el-tag v-else type="danger">{{ $ui("停用") }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("排序")' prop="sort" min-width="100" />
                <el-table-column
                    :label='$ui("更新时间")'
                    prop="update_time"
                    min-width="180"
                ></el-table-column>
                <el-table-column :label='$ui("操作")' width="160" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-if="row.type !== MenuEnum.BUTTON"
                            v-perms="['auth.menu/add']"
                            type="primary"
                            link
                            @click="handleAdd(row.id)"
                        >
                            {{ $ui("新增") }}
                        </el-button>
                        <el-button
                            v-perms="['auth.menu/edit']"
                            type="primary"
                            link
                            @click="handleEdit(row)"
                        >
                            {{ $ui("编辑") }}
                        </el-button>
                        <el-button
                            v-perms="['auth.menu/delete']"
                            type="danger"
                            link
                            @click="handleDelete(row.id)"
                        >
                            {{ $ui("删除") }}
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" @close="showEdit = false" />
    </div>
</template>
<script lang="ts" setup name="permission-menu">
import { translateUiText } from "@/i18n";
import type { ElTable } from 'element-plus'

import { menuDelete, menuLists } from '@/api/perms/menu'
import { MenuEnum } from '@/enums/appEnums'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

import EditPopup from './edit.vue'

const tableRef = shallowRef<InstanceType<typeof ElTable>>()
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
let isExpand = false
const showEdit = ref(false)
const { pager, getLists } = usePaging({
    fetchFun: menuLists,
    params: {
        page_type: 0
    }
})

const handleAdd = async (id?: number) => {
    showEdit.value = true
    await nextTick()
    if (id) {
        editRef.value?.setFormData({
            pid: id
        })
    }
    editRef.value?.open('add')
}

const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit')
    editRef.value?.getDetail(data)
}

const handleDelete = async (id: number) => {
    await feedback.confirm(translateUiText("确定要删除？"))
    await menuDelete({ id })
    getLists()
}

const handleExpand = () => {
    isExpand = !isExpand
    toggleExpand(pager.lists, isExpand)
}

const toggleExpand = (children: any[], unfold = true) => {
    for (const key in children) {
        tableRef.value?.toggleRowExpansion(children[key], unfold)
        if (children[key].children) {
            toggleExpand(children[key].children!, unfold)
        }
    }
}

getLists()
</script>
