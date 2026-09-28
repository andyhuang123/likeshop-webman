<template>
    <el-dropdown class="language-switcher px-2" trigger="click" @command="changeLocale">
        <button
            type="button"
            class="h-full cursor-pointer"
            :aria-label="t('language.label')"
            :title="t('language.label')"
        >
            {{ locale === 'en-US' ? 'EN' : '中文' }}
        </button>
        <template #dropdown>
            <el-dropdown-menu>
                <el-dropdown-item command="zh-CN">{{ $ui("简体中文") }}</el-dropdown-item>
                <el-dropdown-item command="en-US">English</el-dropdown-item>
            </el-dropdown-menu>
        </template>
    </el-dropdown>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

import { setAppLocale } from '@/i18n'

const { locale, t } = useI18n({ useScope: 'global' })

function changeLocale(value: string): void {
    if (value === 'zh-CN') {
        setAppLocale('zh-CN')
    } else if (value === 'en-US') {
        setAppLocale('en-US')
    }
}
</script>

<style scoped lang="scss">
.language-switcher {
    display: flex;
    align-items: center;
    height: 100%;

    button {
        padding: 0;
        border: 0;
        background: transparent;
        color: inherit;
        font: inherit;
    }
}
</style>
