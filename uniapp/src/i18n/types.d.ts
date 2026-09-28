import 'vue'

declare module 'vue' {
    interface ComponentCustomProperties {
        $ui(source: string): string
    }
}
