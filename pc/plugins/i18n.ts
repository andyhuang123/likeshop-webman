import { createI18n } from 'vue-i18n'

import enUS from '~/i18n/locales/en-US'
import zhCN from '~/i18n/locales/zh-CN'

export type AppLocale = 'zh-CN' | 'en-US'

function normalizeLocale(locale?: string | null): AppLocale {
    return locale && /^en(?:-|$)/i.test(locale) ? 'en-US' : 'zh-CN'
}

export default defineNuxtPlugin((nuxtApp) => {
    const localeCookie = useCookie<AppLocale | null>('app_locale')
    const initialLocale = normalizeLocale(localeCookie.value)
    const i18n = createI18n({
        legacy: false,
        locale: initialLocale,
        fallbackLocale: 'zh-CN',
        messages: {
            'zh-CN': zhCN,
            'en-US': enUS
        }
    })
    const uiMessages = {
        'zh-CN': zhCN.ui.messages,
        'en-US': enUS.ui.messages
    }

    nuxtApp.vueApp.use(i18n)

    const setLocale = (nextLocale: AppLocale) => {
        localeCookie.value = nextLocale
        i18n.global.locale.value = nextLocale
    }

    nuxtApp.hook('app:mounted', () => {
        if (!localeCookie.value) {
            setLocale(normalizeLocale(navigator.language))
        }
    })

    return {
        provide: {
            i18n,
            ui: (source: string) => {
                const locale = normalizeLocale(i18n.global.locale.value)
                return uiMessages[locale][source] ?? source
            },
            setLocale
        }
    }
})
