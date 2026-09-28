<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view
        class="register bg-white min-h-full flex flex-col items-center px-[40rpx] pt-[100rpx] box-border"
    >
        <view class="w-full">
            <view class="text-2xl font-medium mb-[60rpx]">
                {{ $ui(type == 'set' ? '设置登录密码' : '修改登录密码') }}
            </view>
            <u-form borderBottom :label-width="150">
                <u-form-item :label='$ui("原密码")' borderBottom v-if="type != 'set'">
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.old_password"
                        :border="false"
                        :placeholder='$ui("请输入原来的密码")'
                    />
                </u-form-item>
                <u-form-item :label='$ui("新密码")' borderBottom>
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.password"
                        :placeholder='$ui("6-20位数字+字母或符号组合")'
                        :border="false"
                    />
                </u-form-item>
                <u-form-item :label='$ui("确认密码")' borderBottom>
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.password_confirm"
                        :placeholder='$ui("再次输入新密码")'
                        :border="false"
                    />
                </u-form-item>
            </u-form>
            <view class="mt-[100rpx]">
                <u-button type="primary" shape="circle" @click="handleConfirm"> {{ $ui("确定") }} </u-button>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { userChangePwd } from '@/api/user'
import { onLoad } from '@dcloudio/uni-app'
import { reactive, ref } from 'vue'
import { translateUiText as ui } from '@/i18n'

const type = ref('')
const formData = reactive<any>({
    password: '',
    password_confirm: ''
})

const handleConfirm = async () => {
    if (!formData.old_password && type.value != 'set') return uni.$u.toast(ui('请输入原来的密码'))
    if (!formData.password) return uni.$u.toast(ui('请输入密码'))
    if (!formData.password_confirm) return uni.$u.toast(ui('请输入确认密码'))
    if (formData.password != formData.password_confirm) return uni.$u.toast(ui('两次输入的密码不一致'))
    await userChangePwd(formData)
    uni.$u.toast(ui('操作成功'))
    setTimeout(() => {
        uni.navigateBack()
    }, 1500)
}

onLoad((options) => {
    type.value = options.type || ''
    if (type.value == 'set') {
        uni.setNavigationBarTitle({
            title: ui('设置登录密码')
        })
    }
})
</script>

<style lang="scss">
page {
    height: 100%;
}
</style>
