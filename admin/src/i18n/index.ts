import { createI18n } from 'vue-i18n'

import enUS from './locales/en-US'
import zhCN from './locales/zh-CN'

export type AppLocale = 'zh-CN' | 'en-US'

const STORAGE_KEY = 'app_locale'

function normalizeLocale(locale: string | null | undefined): AppLocale {
    if (locale && /^en(?:-US)?$/i.test(locale)) {
        return 'en-US'
    }
    if (locale && /^zh(?:-CN)?$/i.test(locale)) {
        return 'zh-CN'
    }
    return 'zh-CN'
}

function getInitialLocale(): AppLocale {
    if (typeof window === 'undefined') {
        return 'zh-CN'
    }
    const savedLocale = window.localStorage.getItem(STORAGE_KEY)
    return normalizeLocale(savedLocale ?? window.navigator.language)
}

const i18n = createI18n({
    legacy: false,
    locale: getInitialLocale(),
    fallbackLocale: 'zh-CN',
    globalInjection: true,
    messages: {
        'zh-CN': zhCN,
        'en-US': enUS
    }
})

const uiMessages: Record<AppLocale, Record<string, string>> = {
    'zh-CN': zhCN.ui.messages,
    'en-US': enUS.ui.messages
}

export function translateUiText(source: string): string {
    const locale = normalizeLocale(i18n.global.locale.value)
    return uiMessages[locale][source] ?? source
}

export function setAppLocale(locale: AppLocale): void {
    i18n.global.locale.value = locale
    window.localStorage.setItem(STORAGE_KEY, locale)
}

export function getAppLocale(): AppLocale {
    return normalizeLocale(i18n.global.locale.value)
}

export default i18n
