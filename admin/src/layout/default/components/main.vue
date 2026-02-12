<template>
    <main class="main-wrap h-full bg-page">
        <el-scrollbar>
            <div class="p-4">
                <router-view v-if="isRouteShow" v-slot="{ Component, route }">
                    <keep-alive :include="cachedViews" :exclude="excludeViews" :max="50">
                        <component :is="Component" :key="route.fullPath" />
                    </keep-alive>
                </router-view>
            </div>
        </el-scrollbar>
    </main>
</template>

<script setup lang="ts">
import useAppStore from '@/stores/modules/app'
import useTabsStore from '@/stores/modules/multipleTabs'
import useSettingStore from '@/stores/modules/setting'

const appStore = useAppStore()
const tabsStore = useTabsStore()
const settingStore = useSettingStore()
const isRouteShow = computed(() => appStore.isRouteShow)

// 需要排除缓存的页面组件名称
const excludeViews = ['Error403', 'Error404', 'Login', 'Redirect']

// 缓存视图列表
const cachedViews = computed(() => {
    if (settingStore.openMultipleTabs) {
        // 返回缓存列表，过滤掉需要排除的页面
        return tabsStore.getCacheTabList.filter((name: string) => !excludeViews.includes(name))
    }
    // 不开启多标签时，不缓存任何页面
    return []
})
</script>

<style></style>

