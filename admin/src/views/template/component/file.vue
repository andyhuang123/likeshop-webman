<template>
    <div>
        <el-card :header='$ui("基础使用")' shadow="never" class="!border-none">
            <div class="flex flex-wrap">
                <div class="flex m-4">
                    <div class="mr-4">{{ $ui("选择图片：") }}</div>
                    <material-picker v-model="state.value1" />
                </div>
                <div class="flex m-4">
                    <div class="mr-4">{{ $ui("选择视频：") }}</div>
                    <material-picker type="video" v-model="state.value3" />
                </div>
                <div class="flex flex-1 m-4">
                    <div class="mr-4">{{ $ui("多张图片：") }}</div>
                    <div class="flex-1">
                        <!-- 外层需要有足够的宽度，这样预览图和选择按钮才不会直接换行 -->
                        <material-picker :limit="4" v-model="state.value2" />
                    </div>
                </div>
            </div>
        </el-card>
        <el-card :header='$ui("进阶用法")' shadow="never" class="!border-none mt-4">
            <div class="flex flex-wrap">
                <div class="flex m-4">
                    <div class="mr-4">{{ $ui("自定义选择器大小：") }}</div>
                    <material-picker size="60px" v-model="state.value4" />
                </div>
                <div class="flex m-4">
                    <div class="mr-4">{{ $ui("使用插槽：") }}</div>
                    <material-picker v-model="state.value5">
                        <template #upload>
                            <el-button>{{ $ui("选择文件") }}</el-button>
                        </template>
                    </material-picker>
                </div>
                <div class="flex m-4">
                    <div class="mr-4">{{ $ui("选出地址不带域名：") }}</div>
                    <material-picker :exclude-domain="true" v-model="state.value6" />
                </div>
            </div>
            <div>
                <div class="flex m-4 items-center">
                    <div class="w-20 flex-none">{{ $ui("带域名：") }}</div>
                    <el-input class="w-[500px]" :model-value="state.value5" />
                </div>
                <div class="flex m-4 items-center">
                    <div class="w-20 flex-none">{{ $ui("不带域名：") }}</div>
                    <el-input class="w-[500px]" :model-value="state.value6" />
                </div>
            </div>
        </el-card>
    </div>
</template>
<script lang="ts" setup>
const state = reactive({
    value1: '',
    value2: [],
    value3: '',
    value4: '',
    value5: '',
    value6: ''
})
</script>
