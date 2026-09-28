<template>
    <div>
        <ElDropdown v-if="userStore.isLogin" @command="handleCommand">
            <div class="flex items-center">
                <ElAvatar :size="25" :src="userStore.userInfo.avatar" />
                <div class="ml-1 text-white text-lg flex">
                    <span class="mr-2">{{ $ui("个人中心") }}</span>
                    <ElIcon><ArrowDown /></ElIcon>
                </div>
            </div>
            <template #dropdown>
                <ElDropdownMenu>
                    <NuxtLink to="/user/info">
                        <ElDropdownItem command="user">{{ $ui("个人信息") }}</ElDropdownItem>
                    </NuxtLink>
                    <NuxtLink to="/user/collection">
                        <ElDropdownItem command="collect">
                            {{ $ui("我的收藏") }}
                        </ElDropdownItem>
                    </NuxtLink>
                    <NuxtLink to="/account/security">
                        <ElDropdownItem command="account">
                            {{ $ui("账号安全") }}
                        </ElDropdownItem>
                    </NuxtLink>
                    <ElDropdownItem command="logout">{{ $ui("退出登录") }}</ElDropdownItem>
                </ElDropdownMenu>
            </template>
        </ElDropdown>

        <div v-else class="cursor-pointer text-lg" @click="handleToLogin">
            {{ $ui("登录/注册") }}
        </div>
    </div>
</template>
<script lang="ts" setup>
import {
    ElAvatar,
    ElDropdown,
    ElDropdownMenu,
    ElDropdownItem,
    ElIcon
} from 'element-plus'
import { ArrowDown } from '@element-plus/icons-vue'
import { useUserStore } from '@/stores/user'
import { PopupTypeEnum, useAccount } from '../account/useAccount'
import feedback from '~~/utils/feedback'
import { logout } from '~~/api/account'
const { setPopupType, toggleShowPopup } = useAccount()
const userStore = useUserStore()

const handleToLogin = () => {
    setPopupType(PopupTypeEnum.LOGIN)
    toggleShowPopup(true)
}

const handleCommand = async (command: string) => {
    switch (command) {
        case 'logout':
            await feedback.confirm(useNuxtApp().$ui('确定退出登录吗？'))
            await logout()
            userStore.logout()
    }
}
</script>

<style lang="scss" scoped></style>
