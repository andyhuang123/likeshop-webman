import request from '@/utils/request'

// 盲盒列表
export function getBlindBoxLists(params: any) {
    return request.get({ url: '/marketing/blind_box/lists', params })
}

// 盲盒详情
export function getBlindBoxDetail(params: any) {
    return request.get({ url: '/marketing/blind_box/detail', params })
}

// 购买盲盒 (下单)
export function buyBlindBox(data: any) {
    return request.post({ url: '/marketing/blind_box_order/create', data })
}

// 盒柜列表
export function getBlindBoxRecords(params: any) {
    return request.get({ url: '/marketing/blind_box_record/lists', params })
}

// 提货
export function shipBlindBox(data: any) {
    return request.post({ url: '/marketing/blind_box_record/ship', data })
}

// 回收
export function recycleBlindBox(data: any) {
    return request.post({ url: '/marketing/blind_box_record/recycle', data })
}
