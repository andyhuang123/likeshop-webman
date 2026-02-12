CREATE TABLE `la_product` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品ID',
  `category_id` int(11) UNSIGNED NOT NULL COMMENT '商品分类ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '商品描述',
  `main_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '商品主图',
  `price` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '商品默认价格（可作为参考价或无SKU时使用）',
  `stock` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品总库存（所有SKU库存之和）',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '商品状态：0-下架，1-上架',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品主表';


CREATE TABLE `la_product_attribute` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '属性ID',
  `product_id` int(11) UNSIGNED NOT NULL COMMENT '所属商品ID',
  `attribute_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '属性名称（如：颜色、尺码）',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `product_id_attribute_name`(`product_id`, `attribute_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品属性定义表';

CREATE TABLE `la_product_attribute_value` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '属性值ID',
  `attribute_id` int(11) UNSIGNED NOT NULL COMMENT '所属属性定义ID（la_product_attribute.id）',
  `attribute_value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '属性值（如：红色、S）',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `attribute_id_value`(`attribute_id`, `attribute_value`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品属性值表';

CREATE TABLE `la_product_sku` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'SKU ID',
  `product_id` int(11) UNSIGNED NOT NULL COMMENT '所属商品ID',
  `sku_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'SKU编码（可选，用于外部系统对接）',
  `attribute_value_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '属性值ID组合，逗号分隔，例如：1,3,5',
  `attribute_value_names` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '属性值名称组合，逗号分隔，例如：红色,S,套餐A',
  `price` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'SKU价格',
  `stock` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'SKU库存',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'SKU图片（可选，如果不同SKU有不同图片）',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `product_id_attribute_value_ids`(`product_id`, `attribute_value_ids`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品SKU表';