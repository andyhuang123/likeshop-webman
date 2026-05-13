<?php
namespace app\adminapi\logic\product;

use app\common\logic\BaseLogic;
use app\common\model\product\Product;
use app\common\model\product\ProductAttribute;
use app\common\model\product\ProductAttributeValue;
use app\common\model\product\ProductSku;
use think\facade\Db;
use Exception;

class ProductLogic extends BaseLogic
{
    public static function add(array $params): void
    {
        Db::transaction(function () use ($params) {
            $product = Product::create([
                'name' => $params['name'],
                'sku' => $params['sku'] ?? '',
                'category_id' => $params['category_id'],
                'price' => $params['price'],
                'stock' => $params['stock'] ?? 0,
                'main_image' => $params['main_image'] ?? '',
                'description' => $params['description'] ?? '',
                'sort' => $params['sort'] ?? 0,
                'status' => $params['status'],
                'product_images' => $params['product_images'] ?? [],
                'market_price' => $params['market_price'] ?? 0,
                'cost_price' => $params['cost_price'] ?? 0,
                'weight' => $params['weight'] ?? 0,
                'volume' => $params['volume'] ?? 0,
                'brand_id' => $params['brand_id'] ?? 0,
            ]);
            
            self::saveSpecs($product->id, $params['spec_type'] ?? 1, $params['specs'] ?? [], $params['skus'] ?? []);
        });
    }

    public static function edit(array $params): bool
    {
        try {
            Db::transaction(function () use ($params) {
                Product::update([
                    'id' => $params['id'],
                    'name' => $params['name'],
                    'sku' => $params['sku'] ?? '',
                    'category_id' => $params['category_id'],
                    'price' => $params['price'],
                    'stock' => $params['stock'] ?? 0,
                    'main_image' => $params['main_image'] ?? '',
                    'description' => $params['description'] ?? '',
                    'sort' => $params['sort'] ?? 0,
                    'status' => $params['status'],
                    'product_images' => $params['product_images'] ?? [],
                    'market_price' => $params['market_price'] ?? 0,
                    'cost_price' => $params['cost_price'] ?? 0,
                    'weight' => $params['weight'] ?? 0,
                    'volume' => $params['volume'] ?? 0,
                    'brand_id' => $params['brand_id'] ?? 0,
                ]);
                
                self::saveSpecs($params['id'], $params['spec_type'] ?? 1, $params['specs'] ?? [], $params['skus'] ?? []);
            });
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function delete(array $params): void
    {
        Product::destroy($params['id']);
        // 软删除关联数据？或者物理删除？这里暂不处理关联表，因为主表软删除
    }

    public static function detail($params): array
    {
        $product = Product::findOrEmpty($params['id']);
        if ($product->isEmpty()) {
            return [];
        }
        
        $data = $product->toArray();
        $data['image_url'] = $product->image_url;

        // 获取规格信息
        $specs = [];
        $skus = [];
        
        // 查询属性
        $attributes = ProductAttribute::where('product_id', $product->id)->select();
        foreach ($attributes as $attr) {
            $values = ProductAttributeValue::where('attribute_id', $attr->id)->column('attribute_value');
            $specs[] = [
                'name' => $attr->attribute_name,
                'values' => $values
            ];
        }

        // 查询SKU
        $skuList = ProductSku::where('product_id', $product->id)->select();
        foreach ($skuList as $sku) {
            $skus[] = [
                'id' => $sku->id,
                'value_ids' => $sku->attribute_value_ids,
                'price' => $sku->price,
                'stock' => $sku->stock,
                'image' => $sku->image,
                'sku_code' => $sku->sku_code,
                'value_names' => $sku->attribute_value_names
            ];
        }
        
        // 简单判断单/多规格
        $data['spec_type'] = empty($specs) ? 1 : 2; 
        $data['specs'] = $specs;
        $data['skus'] = $skus;

        return $data;
    }

    public static function updateStatus(array $params): bool
    {
        Product::update([
            'id' => $params['id'],
            'status' => $params['status'],
        ]);
        return true;
    }

    /**
     * 保存规格信息
     */
    private static function saveSpecs($productId, $specType, $specs, $skus)
    {
        // 1. 获取现有SKU，以便复用ID
        // 这里的key使用attribute_value_names，因为我们假设前端传来的specs顺序和skus里的value_names顺序一致
        $existingSkus = ProductSku::where('product_id', $productId)->select()->toArray();
        $skuMap = [];
        foreach ($existingSkus as $s) {
            $skuMap[$s['attribute_value_names']] = $s;
        }

        // 2. 清理旧的属性和属性值 (保持原有逻辑，全删全加，简单可靠)
        $attrIds = ProductAttribute::where('product_id', $productId)->column('id');
        if (!empty($attrIds)) {
            ProductAttributeValue::where('attribute_id', 'in', $attrIds)->delete();
            ProductAttribute::where('id', 'in', $attrIds)->delete();
        }

        $keptSkuIds = [];

        if ($specType == 1) {
            // 单规格
            $product = Product::find($productId);
            $data = [
                'product_id' => $productId,
                'sku_code' => $product->sku,
                'attribute_value_ids' => '',
                'attribute_value_names' => '',
                'price' => $product->price,
                'stock' => $product->stock,
                'image' => $product->main_image,
                'market_price' => $product->market_price,
                'cost_price' => $product->cost_price,
                'weight' => $product->weight,
                'volume' => $product->volume,
            ];

            // 尝试复用单规格SKU (通常是空名称)
            if (isset($skuMap[''])) {
                $oldSku = $skuMap[''];
                ProductSku::update($data, ['id' => $oldSku['id']]);
                $keptSkuIds[] = $oldSku['id'];
            } else {
                // 如果没有找到，创建新的
                $newSku = ProductSku::create($data);
                $keptSkuIds[] = $newSku->id;
            }
        } else {
            // 多规格
            // 1. 保存属性名和属性值
            $specMap = []; 
            foreach ($specs as $spec) {
                $attrModel = ProductAttribute::create([
                    'product_id' => $productId,
                    'attribute_name' => $spec['name']
                ]);
                
                foreach ($spec['values'] as $valName) {
                    $valModel = ProductAttributeValue::create([
                        'attribute_id' => $attrModel->id,
                        'attribute_value' => $valName
                    ]);
                    $specMap[$spec['name'] . ':' . $valName] = $valModel->id;
                }
            }

            // 2. 保存 SKU
            foreach ($skus as $sku) {
                $valueNamesStr = $sku['value_names'];
                $valueNames = explode(',', $valueNamesStr);
                
                // 解析新的 value_ids
                $valueIds = [];
                foreach ($specs as $index => $spec) {
                    if (isset($valueNames[$index])) {
                        $valName = $valueNames[$index];
                        $key = $spec['name'] . ':' . $valName;
                        if (isset($specMap[$key])) {
                            $valueIds[] = $specMap[$key];
                        }
                    }
                }
                
                $data = [
                    'product_id' => $productId,
                    'sku_code' => $sku['sku_code'] ?? '',
                    'attribute_value_ids' => implode(',', $valueIds),
                    'attribute_value_names' => $valueNamesStr,
                    'price' => $sku['price'],
                    'stock' => $sku['stock'],
                    'image' => $sku['image'] ?? '',
                    'market_price' => $sku['market_price'] ?? 0,
                    'cost_price' => $sku['cost_price'] ?? 0,
                    'weight' => $sku['weight'] ?? 0,
                    'volume' => $sku['volume'] ?? 0,
                ];

                if (isset($skuMap[$valueNamesStr])) {
                    $oldSku = $skuMap[$valueNamesStr];
                    ProductSku::update($data, ['id' => $oldSku['id']]);
                    $keptSkuIds[] = $oldSku['id'];
                } else {
                    $newSku = ProductSku::create($data);
                    $keptSkuIds[] = $newSku->id;
                }
            }
            
            // 更新主表总库存
            $totalStock = array_sum(array_column($skus, 'stock'));
            Product::update(['id' => $productId, 'stock' => $totalStock]);
        }
        
        // 3. 删除未保留的SKU
        if (!empty($keptSkuIds)) {
            ProductSku::where('product_id', $productId)->whereNotIn('id', $keptSkuIds)->delete();
        } else {
            // 如果所有SKU都没保留，则清空
            ProductSku::where('product_id', $productId)->delete();
        }
    }
}
