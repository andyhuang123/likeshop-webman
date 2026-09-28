import type { App } from 'vue'

import i18n, { translateUiText } from '@/i18n'

export default (app: App<Element>) => {
    app.config.globalProperties.$ui = translateUiText
    app.use(i18n)
}
