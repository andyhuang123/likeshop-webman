<template>
    <u-popup v-model="show" mode="center" width="80%" border-radius="20">
        <view class="p-40 text-center relative bg-gradient-to-b from-purple-100 to-white">
            <view class="text-2xl font-bold mb-30 text-purple-600">🎉 恭喜中奖 🎉</view>
            
            <view class="my-40 animate-bounce">
                <u-image 
                    :src="product?.main_image" 
                    width="300rpx" 
                    height="300rpx" 
                    shape="circle"
                    border="4rpx solid #F3E8FF"
                ></u-image>
            </view>
            
            <view class="text-xl font-bold">{{ product?.name }}</view>
            <view class="text-gray-500 mt-10 mb-40">商品已放入您的盒柜</view>
            
            <view class="flex justify-center space-x-20">
                <u-button shape="circle" @click="close">继续购买</u-button>
                <u-button type="primary" shape="circle" @click="toCabinet">去查看</u-button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
    modelValue: boolean,
    product: any
}>()

const emit = defineEmits(['update:modelValue'])

const show = ref(props.modelValue)

watch(() => props.modelValue, (val) => {
    show.value = val
})

watch(show, (val) => {
    emit('update:modelValue', val)
})

const close = () => {
    show.value = false
}

const toCabinet = () => {
    show.value = false
    uni.navigateTo({
        url: '/pages/marketing/blind_box/cabinet'
    })
}
</script>

<style lang="scss" scoped>
.animate-bounce {
    animation: bounce 2s infinite;
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20rpx); }
}
</style>
