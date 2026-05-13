<template>
    <view class="cabinet p-20">
        <!-- 顶部 Tab -->
        <u-tabs 
            :list="tabs" 
            :current="currentTab" 
            @change="changeTab"
            active-color="#ff0000"
            inactive-color="#666"
        ></u-tabs>

        <!-- 列表 -->
        <z-paging ref="paging" v-model="dataList" @query="queryList" class="mt-20">
            <view 
                class="item bg-white p-20 rounded-lg mb-20 flex" 
                v-for="(item, index) in dataList" 
                :key="index"
            >
                <!-- Checkbox -->
                <view 
                    class="flex items-center justify-center mr-20" 
                    v-if="item.status == 0"
                    @click="toggleSelect(item)"
                >
                    <u-checkbox v-model="item.checked" shape="circle"></u-checkbox>
                </view>

                <u-image :src="item.product?.main_image" width="160rpx" height="160rpx" radius="10rpx"></u-image>
                
                <view class="ml-20 flex-1 flex flex-col justify-between">
                    <view>
                        <view class="text-lg font-bold">{{ item.product?.name }}</view>
                        <view class="text-sm text-gray-500 mt-5">来源: {{ item.blind_box?.name }}</view>
                    </view>
                    <view class="flex justify-between items-center">
                        <text class="text-price text-lg text-primary">¥{{ item.product?.price }}</text>
                        <view class="text-sm" :class="statusColor(item.status)">
                            {{ statusText(item.status) }}
                        </view>
                    </view>
                </view>
            </view>
        </z-paging>

        <!-- 底部操作栏 (仅在待提货Tab显示) -->
        <view 
            class="fixed bottom-0 left-0 right-0 bg-white p-20 shadow flex items-center justify-between safe-area-inset-bottom z-50"
            v-if="currentTab === 0"
        >
            <view class="flex items-center">
                <u-checkbox v-model="allChecked" @change="toggleAll">全选</u-checkbox>
                <text class="ml-20 text-sm text-gray-500">已选 {{ selectedCount }} 件</text>
            </view>
            <view class="flex space-x-20">
                <u-button size="mini" type="warning" shape="circle" @click="handleRecycle">回收</u-button>
                <u-button size="mini" type="primary" shape="circle" @click="handleShip">提货</u-button>
            </view>
        </view>
        
        <!-- 回收确认弹窗 -->
        <u-modal v-model="showRecycleModal" title="确认回收" show-cancel-button @confirm="confirmRecycle">
            <view class="p-30 text-center">
                <view>预计返还余额: <text class="text-price text-lg text-primary">{{ recycleAmount }}</text></view>
                <view class="text-xs text-gray-400 mt-10">回收后无法撤销</view>
            </view>
        </u-modal>
        
        <!-- 提货地址选择 (简化版，实际应调用地址组件) -->
        <u-popup v-model="showAddress" mode="bottom" height="600rpx">
             <view class="p-30">
                 <view class="text-lg font-bold mb-20">选择收货地址</view>
                 <!-- 这里应该加载用户地址列表 -->
                 <view class="p-20 bg-gray-50 rounded mb-20" @click="selectAddress(1)">
                     <view>测试用户 13800000000</view>
                     <view class="text-sm text-gray-500">广东省深圳市南山区...</view>
                 </view>
             </view>
        </u-popup>

    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { getBlindBoxRecords, shipBlindBox, recycleBlindBox } from '@/api/marketing/blind_box'

const tabs = [
    { name: '待提货' },
    { name: '已提货' },
    { name: '已回收' }
]
const currentTab = ref(0)
const paging = ref(null)
const dataList = ref([])

const showRecycleModal = ref(false)
const showAddress = ref(false)
const recycleAmount = ref(0)

// 状态处理
const statusText = (status) => ['待提货', '已提货', '已回收'][status]
const statusColor = (status) => ['text-primary', 'text-success', 'text-gray-400'][status]

const changeTab = (index) => {
    currentTab.value = index
    paging.value.reload()
}

const queryList = async (pageNo, pageSize) => {
    const status = currentTab.value
    const res = await getBlindBoxRecords({ page_no: pageNo, page_size: pageSize, status })
    // 为待提货数据添加 checked 属性
    if (status === 0) {
        res.lists.forEach(item => item.checked = false)
    }
    paging.value.complete(res.lists)
}

// 选择逻辑
const toggleSelect = (item) => {
    item.checked = !item.checked
}

const selectedList = computed(() => dataList.value.filter(item => item.checked))
const selectedCount = computed(() => selectedList.value.length)
const allChecked = computed({
    get: () => dataList.value.length > 0 && dataList.value.every(item => item.checked),
    set: (val) => toggleAll(val)
})

const toggleAll = (val) => {
    dataList.value.forEach(item => item.checked = val)
}

// 业务操作
const handleRecycle = () => {
    if (selectedCount.value === 0) return uni.showToast({ title: '请选择商品', icon: 'none' })
    
    // 计算预计回收金额 (前端估算，实际以后端为准)
    let amount = 0
    selectedList.value.forEach(item => {
        amount += (item.blind_box?.price || 0) * 0.2
    })
    recycleAmount.value = amount.toFixed(2)
    showRecycleModal.value = true
}

const confirmRecycle = async () => {
    const ids = selectedList.value.map(item => item.id)
    try {
        await recycleBlindBox({ record_ids: ids })
        uni.showToast({ title: '回收成功' })
        paging.value.reload()
    } catch (e) {}
}

const handleShip = () => {
    if (selectedCount.value === 0) return uni.showToast({ title: '请选择商品', icon: 'none' })
    showAddress.value = true
}

const selectAddress = async (addressId) => {
    const ids = selectedList.value.map(item => item.id)
    try {
        await shipBlindBox({ record_ids: ids, address_id: addressId })
        uni.showToast({ title: '提货成功' })
        showAddress.value = false
        paging.value.reload()
    } catch (e) {}
}
</script>

<style lang="scss" scoped>
.cabinet {
    min-height: 100vh;
    background-color: #f5f5f5;
    padding-bottom: 120rpx;
}
</style>
