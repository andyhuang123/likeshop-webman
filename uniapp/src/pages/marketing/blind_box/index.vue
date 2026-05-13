<template>
    <view class="blind-box-list p-20">
        <z-paging ref="paging" v-model="dataList" @query="queryList">
            <view class="grid grid-cols-2 gap-20">
                <view 
                    class="item bg-white rounded-lg overflow-hidden" 
                    v-for="(item, index) in dataList" 
                    :key="index"
                    @click="toDetail(item.id)"
                >
                    <u-image :src="item.image" width="100%" height="340rpx"></u-image>
                    <view class="p-20">
                        <view class="truncate text-lg font-bold">{{ item.name }}</view>
                        <view class="mt-10 flex justify-between items-center">
                            <text class="text-price text-xl text-primary">¥{{ item.price }}</text>
                            <u-button size="mini" type="primary" shape="circle">立即抽</u-button>
                        </view>
                    </view>
                </view>
            </view>
        </z-paging>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { getBlindBoxLists } from '@/api/marketing/blind_box'

const paging = ref(null)
const dataList = ref([])

const queryList = async (pageNo, pageSize) => {
    try {
        const res = await getBlindBoxLists({ page_no: pageNo, page_size: pageSize })
        paging.value.complete(res.lists)
    } catch (e) {
        paging.value.complete(false)
    }
}

const toDetail = (id) => {
    uni.navigateTo({
        url: `/pages/marketing/blind_box/detail?id=${id}`
    })
}
</script>

<style lang="scss" scoped>
.blind-box-list {
    min-height: 100vh;
    background-color: #f5f5f5;
}
</style>
