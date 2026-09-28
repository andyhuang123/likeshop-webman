/// <reference types="vite/client" />
import { Request } from '@/utils/http/request'
declare global {
    const $request: Request
}

declare module 'vue' {
    interface ComponentCustomProperties {
        $ui(source: string): string
    }
}

declare module '#app' {
    interface NuxtApp {
        $ui(source: string): string
        $setLocale(locale: 'zh-CN' | 'en-US'): void
    }
}
