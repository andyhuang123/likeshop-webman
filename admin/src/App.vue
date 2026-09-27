<script setup lang="ts">
import { useDark, useThrottleFn, useWindowSize } from '@vueuse/core'
import en from 'element-plus/es/locale/lang/en'
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import { useI18n } from 'vue-i18n'

import { ScreenEnum } from './enums/appEnums'
import useAppStore from './stores/modules/app'
import useSettingStore from './stores/modules/setting'

const appStore = useAppStore()
const settingStore = useSettingStore()
const { locale } = useI18n({ useScope: 'global' })
const elementLocale = computed(() => (locale.value === 'en-US' ? en : zhCn))
const zIndex = 3000
const isDark = useDark()
onMounted(async () => {
    //设置主题色
    settingStore.setTheme(isDark.value)
})

const { width } = useWindowSize()
watch(
    width,
    useThrottleFn((value) => {
        if (value > ScreenEnum.SM) {
            appStore.setMobile(false)
            appStore.toggleCollapsed(false)
        } else {
            appStore.setMobile(true)
            appStore.toggleCollapsed(true)
        }
        if (value < ScreenEnum.MD) {
            appStore.toggleCollapsed(true)
        }
    }),
    {
        immediate: true
    }
)
</script>

<template>
    <el-config-provider :locale="elementLocale" :z-index="zIndex">
        <router-view />
    </el-config-provider>
</template>

<style></style>
