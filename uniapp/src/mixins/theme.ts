import { useAppStore } from '@/stores/app'
import { useThemeStore } from '@/stores/theme'
import { updateNavigationBarTitle } from '@/i18n'

export default {
    computed: {
        $theme() {
            const themeStore = useThemeStore()
            const appStore = useAppStore()
            return {
                primaryColor: themeStore.primaryColor,
                pageStyle: themeStore.vars,
                navColor: themeStore.navColor,
                navBgColor: themeStore.navBgColor,
                title: appStore.getWebsiteConfig.shop_name
            }
        }
    },
    onShow() {
        updateNavigationBarTitle()
    }
}
