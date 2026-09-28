import { createI18n } from 'vue-i18n'

import enUS from './locales/en-US'
import zhCN from './locales/zh-CN'

export type AppLocale = 'zh-CN' | 'en-US'

const i18n = createI18n({
    legacy: false,
    locale: 'zh-CN',
    fallbackLocale: 'zh-CN',
    messages: {
        'zh-CN': zhCN,
        'en-US': enUS
    }
})

const uiMessages: Record<AppLocale, Record<string, string>> = {
    'zh-CN': zhCN.ui.messages,
    'en-US': enUS.ui.messages
}

const navigationTitles: Record<string, string> = {
    'pages/news/news': '资讯',
    'pages/login/login': '登录',
    'pages/register/register': '注册',
    'pages/forget_pwd/forget_pwd': '忘记密码',
    'pages/customer_service/customer_service': '联系客服',
    'pages/news_detail/news_detail': '详情',
    'pages/user_set/user_set': '个人设置',
    'pages/collection/collection': '我的收藏',
    'pages/as_us/as_us': '关于我们',
    'pages/change_password/change_password': '修改密码',
    'pages/user_data/user_data': '个人资料',
    'pages/search/search': '搜索',
    'pages/empty/empty': '商城',
    'pages/bind_mobile/bind_mobile': '绑定手机号',
    'pages/payment_result/payment_result': '支付结果',
    'pages/marketing/blind_box/index': '盲盒抽奖',
    'pages/marketing/blind_box/detail': '盲盒详情',
    'pages/marketing/blind_box/cabinet': '我的盒柜',
    'uni_modules/vk-uview-ui/components/u-avatar-cropper/u-avatar-cropper': '头像裁剪',
    'packages/pages/404/404': '404',
    'packages/pages/user_wallet/user_wallet': '我的钱包',
    'packages/pages/recharge/recharge': '充值',
    'packages/pages/recharge_record/recharge_record': '充值记录'
}

function normalizeLocale(locale?: string | null): AppLocale {
    return locale && /^en(?:-|$)/i.test(locale) ? 'en-US' : 'zh-CN'
}

export function initializeAppLocale(): void {
    const savedLocale = uni.getStorageSync('app_locale')
    const deviceLocale = uni.getSystemInfoSync().language
    i18n.global.locale.value = normalizeLocale(savedLocale || deviceLocale)
}

export function getAppLocale(): AppLocale {
    return normalizeLocale(i18n.global.locale.value)
}

export function setAppLocale(locale: AppLocale): void {
    i18n.global.locale.value = locale
    uni.setStorageSync('app_locale', locale)
    updateNavigationBarTitle()
}

export function translateUiText(source: string): string {
    return uiMessages[getAppLocale()][source] ?? source
}

export function updateNavigationBarTitle(): void {
    const pages = getCurrentPages()
    const page = pages[pages.length - 1]
    if (!page) return
    const pageInfo = page as typeof page & { options?: Record<string, string> }
    let title = navigationTitles[page.route]
    if (
        page.route === 'pages/change_password/change_password' &&
        pageInfo.options?.type === 'set'
    ) {
        title = '设置登录密码'
    }
    if (title) {
        uni.setNavigationBarTitle({ title: translateUiText(title) })
    }
}

export default i18n
