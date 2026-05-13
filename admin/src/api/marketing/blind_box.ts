import request from '@/utils/request'

// 盲盒列表
export function blindBoxLists(params: any) {
    return request.get({ url: '/marketing/blind_box/lists', params })
}

// 盲盒详情
export function blindBoxDetail(params: any) {
    return request.get({ url: '/marketing/blind_box/detail', params })
}

// 添加盲盒
export function blindBoxAdd(params: any) {
    return request.post({ url: '/marketing/blind_box/add', params })
}

// 编辑盲盒
export function blindBoxEdit(params: any) {
    return request.post({ url: '/marketing/blind_box/edit', params })
}

// 删除盲盒
export function blindBoxDelete(params: any) {
    return request.post({ url: '/marketing/blind_box/delete', params })
}

// 修改状态
export function blindBoxStatus(params: any) {
    return request.post({ url: '/marketing/blind_box/status', params })
}

// 设置奖品
export function blindBoxSetPrizes(params: any) {
    return request.post({ url: '/marketing/blind_box/set_prizes', params })
}
