<template>
    <div class="article-lists">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item :label='$ui("文章标题")'>
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.title"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item :label='$ui("栏目名称")'>
                    <el-select class="w-[280px]" v-model="queryParams.cid">
                        <el-option :label='$ui("全部")' value />
                        <el-option
                            v-for="item in optionsData.article_cate"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item :label='$ui("文章状态")'>
                    <el-select class="w-[280px]" v-model="queryParams.is_show">
                        <el-option :label='$ui("全部")' value />
                        <el-option :label='$ui("显示")' :value="1" />
                        <el-option :label='$ui("隐藏")' :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">{{ $ui("查询") }}</el-button>
                    <el-button @click="resetParams">{{ $ui("重置") }}</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <router-link
                    v-perms="['article.article/add', 'article.article/add:edit']"
                    :to="{
                        path: getRoutePath('article.article/add:edit')
                    }"
                >
                    <el-button type="primary" class="mb-4">
                        <template #icon>
                            <icon name="el-icon-Plus" />
                        </template>
                        {{ $ui("发布文章") }}
                    </el-button>
                </router-link>
            </div>
            <el-table
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                element-loading-svg-view-box="-10, -10, 50, 50"
                element-loading-svg='<path d="M30.365 5.407c-3.623-1.407-7.614-1.407-11.238 0-3.66 1.407-6.356 4.105-7.87 7.644-1.477 3.54-1.477 7.457 0 10.997 1.477 3.54 4.173 6.236 7.87 7.643 3.624 1.44 7.615 1.44 11.238 0 3.66-1.44 6.356-4.136 7.87-7.643 1.477-3.54 1.477-7.457 0-10.997-1.514-3.54-4.21-6.237-7.87-7.644zm-1.414 4.828c.937.354 1.69.937 2.14 1.69.45.752.63 1.62.45 2.49-.18.908-.63 1.69-1.327 2.277-.697.587-1.54.907-2.49.907-.95 0-1.793-.32-2.49-.907-.697-.587-1.147-1.37-1.327-2.277-.18-.87.03-1.737.45-2.49.45-.752 1.203-1.336 2.14-1.69.937-.354 1.96-.442 2.964-.088.977.354 1.887.977 2.49 1.886.604-.908 1.513-1.532 2.49-1.886 1.004-.354 2.027-.265 2.965.088.937.354 1.69.937 2.14 1.69.45.752.63 1.62.45 2.49-.18.908-.63 1.69-1.327 2.277-.697.587-1.54.907-2.49.907-.95 0-1.793-.32-2.49-.907-.697-.587-1.147-1.37-1.327-2.277-.18-.87.03-1.737.45-2.49.45-.752 1.203-1.336 2.14-1.69.937-.354 1.96-.442 2.964-.088.977.354 1.887.977 2.49 1.886.604-.908 1.513-1.532 2.49-1.886 1.004-.354 2.027-.265 2.965.088z" fill="#4A5DFF"/>'
                class="table-loading"
            >
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column :label='$ui("封面")' min-width="100">
                    <template #default="{ row }">
                        <image-contain
                            v-if="row.image"
                            :src="row.image"
                            :width="60"
                            :height="45"
                            :preview-src-list="[row.image]"
                            preview-teleported
                            fit="contain"
                        />
                    </template>
                </el-table-column>
                <el-table-column
                    :label='$ui("标题")'
                    prop="title"
                    min-width="160"
                    show-tooltip-when-overflow
                />
                <el-table-column :label='$ui("栏目")' prop="cate_name" min-width="100" />
                <el-table-column :label='$ui("作者")' prop="author" min-width="120" />
                <el-table-column :label='$ui("浏览量")' prop="click" min-width="100" />
                <el-table-column :label='$ui("状态")' min-width="100">
                    <template #default="{ row }">
                        <el-switch
                            v-perms="['article.article/updateStatus']"
                            v-model="row.is_show"
                            :active-value="1"
                            :inactive-value="0"
                            @change="changeStatus($event, row.id)"
                        />
                    </template>
                </el-table-column>
                <el-table-column :label='$ui("排序")' prop="sort" min-width="100" />
                <el-table-column :label='$ui("发布时间")' prop="create_time" min-width="120" />
                <el-table-column :label='$ui("操作")' width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-perms="['article.article/edit', 'article.article/add:edit']"
                            type="primary"
                            link
                        >
                            <router-link
                                :to="{
                                    path: getRoutePath('article.article/add:edit'),
                                    query: {
                                        id: row.id
                                    }
                                }"
                            >
                                {{ $ui("编辑") }}
                            </router-link>
                        </el-button>
                        <el-button
                            v-perms="['article.article/delete']"
                            type="danger"
                            link
                            @click="handleDelete(row.id)"
                        >
                            {{ $ui("删除") }}
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>
<script lang="ts" setup name="articleLists">
import { translateUiText } from "@/i18n";
import { articleCateAll, articleDelete, articleLists, articleStatus } from '@/api/article'
import { useDictOptions } from '@/hooks/useDictOptions'
import { usePaging } from '@/hooks/usePaging'
import { getRoutePath } from '@/router'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    title: '',
    cid: '',
    is_show: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: articleLists,
    params: queryParams
})

const { optionsData } = useDictOptions<{
    article_cate: any[]
}>({
    article_cate: {
        api: articleCateAll
    }
})

const changeStatus = async (is_show: any, id: number) => {
    try {
        await articleStatus({ id, is_show })
        getLists()
    } catch (error) {
        getLists()
    }
}

const handleDelete = async (id: number) => {
    await feedback.confirm(translateUiText("确定要删除？"))
    await articleDelete({ id })
    getLists()
}

onActivated(() => {
    getLists()
})

getLists()
</script>
