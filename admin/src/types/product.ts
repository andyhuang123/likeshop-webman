export interface ProductItem {
    id: number
    name: string
    sku: string
    category_id: number
    price: number
    stock: number
    main_image: string
    description: string
    sort: number
    status: number
    create_time: number
    update_time?: number
    image_url?: string
    is_show_desc?: string
}

export interface ProductSpec {
    name: string
    values: string[]
    tempValue?: string
}

export interface ProductSku {
    id?: number
    value_ids?: string
    value_names: string
    price: number
    market_price?: number
    cost_price?: number
    stock: number
    weight?: number
    volume?: number
    sku_code: string
    image: string
}

export interface ProductFormData {
    id?: string
    name: string
    sku: string
    main_image: string
    product_images: string[]
    category_id: number | ''
    price: number
    market_price?: number
    cost_price?: number
    stock: number
    weight?: number
    volume?: number
    description: string
    sort: number
    status: number
    spec_type: 1 | 2
    specs: ProductSpec[]
    skus: ProductSku[]
}

export interface ProductCateItem {
    id: number
    name: string
    sort: number
    is_show: number
    create_time: number
    update_time?: number
    product_count?: number
    is_show_desc?: string
}

export interface ProductParams {
    name?: string
    category_id?: number | ''
    status?: number | ''
    page?: number
    limit?: number
}

export interface ProductListsResult {
    lists: ProductItem[]
    count: number
}

export interface ProductCateParams {
    name?: string
    is_show?: number | ''
    page?: number
    limit?: number
}

export interface ProductCateListsResult {
    lists: ProductCateItem[]
    count: number
}
