<!-- 系统环境 -->
<template>
    <div class="system-environment">
        <el-card class="!border-none" shadow="never">
            <div>{{ $ui("服务器信息") }}</div>
            <div class="mt-4">
                <el-table :data="info.server">
                    <el-table-column prop="param" :label='$ui("参数")'></el-table-column>
                    <el-table-column prop="value" :label='$ui("值")'></el-table-column>
                </el-table>
            </div>
        </el-card>

        <el-card shadow="never" class="!border-none mt-4">
            <div>{{ $ui("PHP环境要求") }}</div>
            <div class="mt-4">
                <el-table :data="info.env">
                    <el-table-column prop="option" :label='$ui("选项")'></el-table-column>
                    <el-table-column prop="require" :label='$ui("要求")'></el-table-column>
                    <el-table-column :label='$ui("状态")'>
                        <template #default="scope">
                            <icon
                                v-if="scope.row.status"
                                name="el-icon-Select"
                                class="text-success"
                            />
                            <icon v-else name="el-icon-CloseBold" class="text-danger" />
                        </template>
                    </el-table-column>
                    <el-table-column prop="remark" :label='$ui("说明及帮助")'></el-table-column>
                </el-table>
            </div>
        </el-card>

        <el-card shadow="never" class="!border-none mt-4">
            <div>{{ $ui("目录权限") }}</div>
            <div class="mt-4">
                <el-table :data="info.auth">
                    <el-table-column prop="dir" :label='$ui("选项")'></el-table-column>
                    <el-table-column prop="require" :label='$ui("要求")'></el-table-column>
                    <el-table-column :label='$ui("状态")'>
                        <template #default="scope">
                            <icon
                                v-if="scope.row.status"
                                name="el-icon-Select"
                                class="text-success"
                            />
                            <icon v-else name="el-icon-CloseBold" class="text-danger" />
                        </template>
                    </el-table-column>
                    <el-table-column prop="remark" :label='$ui("说明及帮助")'></el-table-column>
                </el-table>
            </div>
        </el-card>
    </div>
</template>

<script lang="ts" setup name="environment">
import { systemInfo } from '@/api/setting/system'

const info = reactive({
    server: [], // 服务器信息
    env: [], // PHP环境要求
    auth: [] // 目录权限
})

const getSystemInfo = () => {
    systemInfo().then((res) => {
        info.server = res.server
        info.env = res.env
        info.auth = res.auth
    })
}

onMounted(() => {
    getSystemInfo()
})
</script>

<style lang="scss" scoped></style>
