<template>
    <view class="blind-box-detail pb-120">
        <u-image :src="detail.image" width="100%" height="750rpx"></u-image>
        
        <view class="p-30 bg-white">
            <view class="text-2xl font-bold">{{ detail.name }}</view>
            <view class="mt-20 text-price text-3xl text-primary font-bold">¥{{ detail.price }}</view>
        </view>

        <!-- 奖池公示 -->
        <view class="mt-20 bg-white p-30">
            <view class="text-lg font-bold mb-20">{{ $ui("奖池公示") }}</view>
            <view class="space-y-20">
                <view 
                    v-for="(item, index) in detail.details" 
                    :key="index"
                    class="flex items-center justify-between p-20 bg-gray-50 rounded"
                >
                    <view class="flex items-center">
                        <u-image :src="item.product?.main_image" width="80rpx" height="80rpx" radius="8rpx"></u-image>
                        <view class="ml-20">
                            <view class="text-base">{{ item.product?.name }}</view>
                            <view class="text-xs text-gray-500 mt-5">{{ $ui("价值: ¥") }}{{ item.product?.price }}</view>
                        </view>
                    </view>
                    <view class="text-sm text-primary font-medium">
                        {{ $ui("概率:") }} {{ item.probability }}%
                    </view>
                </view>
            </view>
        </view>

        <!-- 底部操作栏 -->
        <view class="fixed bottom-0 left-0 right-0 bg-white p-20 shadow flex items-center justify-between safe-area-inset-bottom z-50">
            <view class="flex-1 mr-20">
                <u-button shape="circle" @click="toCabinet">{{ $ui("我的盒柜") }}</u-button>
            </view>
            <view class="flex-2">
                <u-button type="primary" shape="circle" @click="handleBuy">{{ $ui("立即购买 (¥") }}{{ detail.price }})</u-button>
            </view>
        </view>

        <!-- 支付方式弹窗 -->
        <u-popup v-model="showPay" mode="bottom" border-radius="24">
            <view class="p-30">
                <view class="text-center text-lg font-bold mb-30">{{ $ui("选择支付方式") }}</view>
                <!-- 这里需要集成支付组件，简化处理 -->
                <u-button type="primary" shape="circle" @click="confirmPay">{{ $ui("微信支付") }}</u-button>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getBlindBoxDetail, buyBlindBox } from '@/api/marketing/blind_box'
import { pay } from '@/utils/pay' // 假设有封装好的支付工具
import { translateUiText as ui } from '@/i18n'

const detail = ref<any>({})
const blindBoxId = ref(0)
const showPay = ref(false)

onLoad((options) => {
    blindBoxId.value = options.id
    getDetail()
})

const getDetail = async () => {
    const res = await getBlindBoxDetail({ id: blindBoxId.value })
    detail.value = res
}

const toCabinet = () => {
    uni.navigateTo({
        url: '/pages/marketing/blind_box/cabinet'
    })
}

const handleBuy = () => {
    showPay.value = true
}

const confirmPay = async () => {
    try {
        const res = await buyBlindBox({ 
            blind_box_id: blindBoxId.value,
            pay_way: 1 // 微信支付
        })
        
        // 调起支付
        await pay(res.pay_way, res.config)
        
        uni.showToast({ title: ui('支付成功'), icon: 'success' })
        showPay.value = false
        
        // 跳转到结果页或弹窗
        // 这里暂时直接跳转到盒柜
        setTimeout(() => {
            toCabinet()
        }, 1500)
    } catch (e) {
        uni.showToast({ title: ui('支付失败'), icon: 'none' })
    }
}
</script>

<style lang="scss" scoped>
.blind-box-detail {
    min-height: 100vh;
    background-color: #f5f5f5;
}
.safe-area-inset-bottom {
    padding-bottom: env(safe-area-inset-bottom);
}
</style>
