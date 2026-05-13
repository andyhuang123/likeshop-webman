import type {
    ProductCateItem,
    ProductCateListsResult,
    ProductCateParams,
    ProductFormData,
    ProductItem,
    ProductListsResult,
    ProductParams
} from '@/types/product'
import request from '@/utils/request'

export function productCateLists(params?: ProductCateParams) {
    return request.get<ProductCateListsResult>({ url: '/product/productCate/lists', params })
}

export function productCateAll(params?: Partial<ProductCateItem>) {
    return request.get<ProductCateItem[]>({ url: '/product/productCate/all', params })
}

export function productCateAdd(params: Partial<ProductCateItem>) {
    return request.post({ url: '/product/productCate/add', params })
}

export function productCateEdit(params: Partial<ProductCateItem> & { id: number }) {
    return request.post({ url: '/product/productCate/edit', params })
}

export function productCateDelete(params: { id: number }) {
    return request.post({ url: '/product/productCate/delete', params })
}

export function productCateDetail(params: { id: number }) {
    return request.get<ProductCateItem>({ url: '/product/productCate/detail', params })
}

export function productCateStatus(params: { id: number; is_show: number }) {
    return request.post({ url: '/product/productCate/updateStatus', params })
}

export function productLists(params?: ProductParams) {
    return request.get<ProductListsResult>({ url: '/product/product/lists', params })
}

export function productAdd(params: ProductFormData) {
    return request.post({ url: '/product/product/add', params })
}

export function productEdit(params: ProductFormData & { id: number }) {
    return request.post({ url: '/product/product/edit', params })
}

export function productDelete(params: { id: number }) {
    return request.post({ url: '/product/product/delete', params })
}

export function productDetail(params: { id: number }) {
    return request.get<ProductItem>({ url: '/product/product/detail', params })
}

export function productStatus(params: { id: number; status: number }) {
    return request.post({ url: '/product/product/updateStatus', params })
}
