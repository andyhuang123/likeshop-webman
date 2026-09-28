/// <reference types="vite/client" />

import type { translateUiText } from './src/i18n'

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $ui: typeof translateUiText
    }
}
