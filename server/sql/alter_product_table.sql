-- 为商品表添加缺失字段
ALTER TABLE `la_product`
ADD COLUMN IF NOT EXISTS `market_price` decimal(10,2) DEFAULT 0.00 COMMENT '市场价',
ADD COLUMN IF NOT EXISTS `cost_price` decimal(10,2) DEFAULT 0.00 COMMENT '成本价',
ADD COLUMN IF NOT EXISTS `weight` decimal(10,2) DEFAULT 0.00 COMMENT '重量（kg）',
ADD COLUMN IF NOT EXISTS `volume` decimal(10,2) DEFAULT 0.00 COMMENT '体积（m³）',
ADD COLUMN IF NOT EXISTS `brand_id` int(11) DEFAULT 0 COMMENT '品牌ID',
ADD COLUMN IF NOT EXISTS `product_images` text COMMENT '商品图集（JSON数组）';

-- 为商品SKU表添加缺失字段（根据模型）
ALTER TABLE `la_product_sku`
ADD COLUMN IF NOT EXISTS `market_price` decimal(10,2) DEFAULT 0.00 COMMENT '市场价',
ADD COLUMN IF NOT EXISTS `cost_price` decimal(10,2) DEFAULT 0.00 COMMENT '成本价',
ADD COLUMN IF NOT EXISTS `weight` decimal(10,2) DEFAULT 0.00 COMMENT '重量（kg）',
ADD COLUMN IF NOT EXISTS `volume` decimal(10,2) DEFAULT 0.00 COMMENT '体积（m³）';

-- 注意：la_product表已有字段映射，使用cid、image、desc、is_show等字段名，模型通过$field映射