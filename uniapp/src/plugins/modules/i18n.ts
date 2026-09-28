import type { App } from 'vue'

import i18n, { initializeAppLocale, translateUiText } from '@/i18n'

export default (app: App) => {
    initializeAppLocale()
    app.use(i18n)
    app.config.globalProperties.$ui = translateUiText
}
