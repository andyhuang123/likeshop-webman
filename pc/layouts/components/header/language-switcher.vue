<template>
    <ElDropdown trigger="click" @command="handleChange">
        <button class="text-white text-sm mx-5">{{ locale === 'en-US' ? 'English' : '简体中文' }}</button>
        <template #dropdown>
            <ElDropdownMenu>
                <ElDropdownItem command="zh-CN">{{ $ui("简体中文") }}</ElDropdownItem>
                <ElDropdownItem command="en-US">English</ElDropdownItem>
            </ElDropdownMenu>
        </template>
    </ElDropdown>
</template>

<script lang="ts" setup>
import { useI18n } from 'vue-i18n'

import type { AppLocale } from '~/plugins/i18n'

const { locale } = useI18n({ useScope: 'global' })
const localeCookie = useCookie<AppLocale>('app_locale')

const handleChange = (value: AppLocale) => {
    localeCookie.value = value
    locale.value = value
}
</script>
