import request from '@/utils/request'

// 商品分类
export function productCateLists(params?: any) {
    return request.get({ url: '/product/productCate/lists', params })
}
export function productCateAll(params?: any) {
    return request.get({ url: '/product/productCate/all', params })
}
export function productCateAdd(params: any) {
    return request.post({ url: '/product/productCate/add', params })
}
export function productCateEdit(params: any) {
    return request.post({ url: '/product/productCate/edit', params })
}
export function productCateDelete(params: any) {
    return request.post({ url: '/product/productCate/delete', params })
}
export function productCateDetail(params: any) {
    return request.get({ url: '/product/productCate/detail', params })
}
export function productCateStatus(params: any) {
    return request.post({ url: '/product/productCate/updateStatus', params })
}

// 商品
export function productLists(params?: any) {
    return request.get({ url: '/product/product/lists', params })
}
export function productAdd(params: any) {
    return request.post({ url: '/product/product/add', params })
}
export function productEdit(params: any) {
    return request.post({ url: '/product/product/edit', params })
}
export function productDelete(params: any) {
    return request.post({ url: '/product/product/delete', params })
}
export function productDetail(params: any) {
    return request.get({ url: '/product/product/detail', params })
}
export function productStatus(params: any) {
    return request.post({ url: '/product/product/updateStatus', params })
}

