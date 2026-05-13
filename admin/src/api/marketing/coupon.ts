import request from '@/utils/request'

// 优惠券列表
export function apiCouponLists(params: any) {
    return request.get({ url: '/marketing/coupon/lists', params })
}

// 优惠券详情
export function apiCouponDetail(params: any) {
    return request.get({ url: '/marketing/coupon/detail', params })
}

// 添加优惠券
export function apiCouponAdd(params: any) {
    return request.post({ url: '/marketing/coupon/add', params })
}

// 编辑优惠券
export function apiCouponEdit(params: any) {
    return request.post({ url: '/marketing/coupon/edit', params })
}

// 删除优惠券
export function apiCouponDelete(params: any) {
    return request.post({ url: '/marketing/coupon/delete', params })
}

// 修改状态
export function apiCouponStatus(params: any) {
    return request.post({ url: '/marketing/coupon/status', params })
}
