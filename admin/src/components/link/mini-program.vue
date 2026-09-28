<template>
    <div class="mini-program h-[530px]">
        <div class="text-xl font-medium">{{ $ui("跳转小程序") }}</div>
        <div class="flex flex-wrap items-center mt-4">
            <div class="w-[86px] text-right">{{ $ui("小程序APPID") }}</div>
            <div class="ml-4 flex-1 min-w-[100px]">
                <el-input
                    class="max-w-[320px]"
                    :model-value="modelValue.query?.appId"
                    :placeholder='$ui("请输入小程序appId")'
                    @input="(value) => handleInput('appId', value)"
                />
            </div>
        </div>
        <div class="flex flex-wrap items-center mt-4">
            <div class="w-[86px] text-right">{{ $ui("小程序路径") }}</div>
            <div class="ml-4 flex-1 min-w-[100px]">
                <el-input
                    class="max-w-[320px]"
                    :model-value="modelValue.query?.path"
                    :placeholder='$ui("请输入小程序路径链接地址")'
                    @input="(value) => handleInput('path', value)"
                />
            </div>
        </div>
        <div class="flex flex-wrap items-center mt-4">
            <div class="w-[86px] text-right">{{ $ui("传递参数") }}</div>
            <div class="ml-4 flex-1 min-w-[100px]">
                <el-input
                    class="max-w-[320px]"
                    :model-value="modelValue.query?.query"
                    :placeholder='$ui("请输入小程序跳转参数(选填)")'
                    @input="(value) => handleInput('query', value)"
                />
            </div>
        </div>
        <div class="form-tips ml-[100px] max-w-[320px]">
            <div>{{ $ui("示例：id=2&ustm=jiny&name=234") }}</div>
            <div class="text-error">
                {{ $ui("注意：不允许输入中文、特殊字符等。如果出现对不起，当前页面无法访问，大概率是跳转参数的问题！！") }}
            </div>
        </div>
        <div class="flex flex-wrap items-center mt-4">
            <div class="w-[86px] text-right">{{ $ui("小程序版本") }}</div>
            <div class="ml-4 flex-1 min-w-[100px]">
                <el-radio-group
                    :model-value="modelValue.query?.env_version"
                    @change="(value) => handleInput('env_version', value)"
                >
                    <el-radio label="release">{{ $ui("正式版") }}</el-radio>
                    <el-radio label="trial">{{ $ui("体验版") }}</el-radio>
                    <el-radio label="develop">{{ $ui("开发版") }}</el-radio>
                </el-radio-group>
            </div>
        </div>

        <div>
            <div class="form-tips ml-[100px] max-w-[320px]">
                <div class="mt-4">
                    {{ $ui("1. 小程序APPID和小程序路径链接地址，小程序路径链接地址请填写小程序的页面路径，如：pages/index/index") }}
                </div>
                <div class="mt-2">
                    <span>{{ $ui("2. 如果是H5(浏览器)中需要跳转到小程序，则需要以下配置--->") }}</span>
                    <a
                        href="https://mp.weixin.qq.com/"
                        class="text-primary"
                        target="_blank"
                        rel="nofollow"
                    >
                        {{ $ui("小程序管理后台 -> 设置 -> 隐私与安全 -> 明文 scheme 拉起此小程序 （点击跳转去配置）") }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'

import { type Link, LinkTypeEnum } from '.'

const props = defineProps({
    modelValue: {
        type: Object as PropType<Link>,
        default: () => ({})
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: Link): void
}>()

const handleInput = (key: 'appId' | 'path' | 'env_version' | 'query', value: string) => {
    emit('update:modelValue', {
        ...props.modelValue,
        name: '小程序跳转',
        query: {
            ...props.modelValue.query,
            [key]: value
        },
        type: LinkTypeEnum.MINI_PROGRAM
    })
}

watch(
    () => props.modelValue,
    (value) => {
        if (!value.query?.env_version) {
            handleInput('env_version', 'release')
        }
    },
    { immediate: true }
)
</script>
