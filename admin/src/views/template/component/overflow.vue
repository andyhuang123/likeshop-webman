<template>
    <div>
        <el-card :header='$ui("基础使用")' shadow="never" class="!border-none">
            <overflow-tooltip class="w-20 m-4" :content='$ui("超出自动打点，悬浮弹窗显示全部内容")' />
            <overflow-tooltip class="w-60 m-4" :content='$ui("超出自动打点，悬浮弹窗显示全部内容")' />
        </el-card>
    </div>
</template>
<script lang="ts" setup></script>
