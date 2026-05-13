<?php
/**
 * Test Script for ProductLogic Refactor Verification
 * Usage: php server/tests/verify_product_refactor.php
 */

define('BASE_PATH', dirname(__DIR__));

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Webman (loads .env, configs, helpers, db, cache, etc.)
require_once __DIR__ . '/../support/bootstrap.php';

use app\adminapi\logic\product\ProductLogic;
use think\facade\Db;

echo "--- Webman Environment Bootstrapped ---\n";

// Helper to clean up previous test data
function cleanup($name) {
    $product = Db::table('la_product')->where('name', $name)->find();
    if ($product) {
        $pid = $product['id'];
        Db::table('la_product')->where('id', $pid)->delete();
        Db::table('la_product_sku')->where('product_id', $pid)->delete();
        // Also clean attributes if needed, though Logic might leave orphans or handle it
        Db::table('la_product_attribute')->where('product_id', $pid)->delete();
        // Attribute values cleanup is harder without IDs, but cascading delete usually handles it if foreign keys exist.
        // Logic::saveSpecs deletes by attribute_id.
        echo "Cleaned up existing product: $name (ID: $pid)\n";
    }
}

$testName = "Test Product Refactor";
cleanup($testName);

// 2. Prepare Test Data
echo "\n--- 1. Create Product ---\n";
$params = [
    'name' => $testName,
    'category_id' => 1, 
    'price' => 200,
    'market_price' => 200,
    'cost_price' => 50,
    'weight' => 1.5,
    'volume' => 0.02,
    'product_images' => ["image1.jpg", "image2.jpg"],
    'spec_type' => 2, // Multi-spec
    'status' => 1,
    'specs' => [
        ['name' => 'Color', 'values' => ['Red', 'Blue']]
    ],
    // Note: ProductLogic expects 'skus', mapping from user prompt 'items'
    'skus' => [
        [
            'value_names' => 'Red',
            'sku_code' => 'SKU_RED',
            'price' => 200,
            'stock' => 10,
            'image' => 'red.jpg',
            'market_price' => 220,
            'cost_price' => 50,
            'weight' => 1.5,
            'volume' => 0.02,
        ],
        [
            'value_names' => 'Blue',
            'sku_code' => 'SKU_BLUE',
            'price' => 205,
            'stock' => 10,
            'image' => 'blue.jpg',
            'market_price' => 225,
            'cost_price' => 55,
            'weight' => 1.5,
            'volume' => 0.02,
        ]
    ]
];

try {
    ProductLogic::add($params);
    echo "ProductLogic::add executed successfully.\n";
} catch (\Throwable $e) {
    echo "ProductLogic::add failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Verify DB
echo "\n--- 2. Verify DB ---\n";
$product = Db::table('la_product')->where('name', $testName)->find();
if (!$product) {
    die("FAILURE: Product not found in la_product table.\n");
}
$pid = $product['id'];
echo "Product created with ID: $pid\n";
echo "Name: {$product['name']}, Price: {$product['price']}\n";

$skus = Db::table('la_product_sku')->where('product_id', $pid)->select()->toArray();
echo "SKUs found: " . count($skus) . " (Expected 2)\n";
if (count($skus) !== 2) {
    die("FAILURE: Expected 2 SKUs, found " . count($skus) . "\n");
}

$skuMap = [];
foreach ($skus as $sku) {
    echo " - SKU ID: {$sku['id']}, Code: {$sku['sku_code']}, Price: {$sku['price']}\n";
    $skuMap[$sku['sku_code']] = $sku['id'];
}

// 4. Edit Product - Check Persistence
echo "\n--- 3. Edit Product (Update Price) ---\n";
// We need to fetch the product details first or just construct params carefully.
// Logic::edit requires 'id'.
$editParams = $params;
$editParams['id'] = $pid;
// Modify Red price
$editParams['skus'][0]['price'] = 300; 
// Logic::saveSpecs logic relies on 'value_names' to match existing SKUs.
// If we pass the same value_names, it should update.

try {
    $result = ProductLogic::edit($editParams);
    if ($result) {
        echo "ProductLogic::edit executed successfully.\n";
    } else {
        echo "ProductLogic::edit returned false. Error: " . ProductLogic::getError() . "\n";
        exit(1);
    }
} catch (\Throwable $e) {
    echo "ProductLogic::edit failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Verify Persistence
$skusAfter = Db::table('la_product_sku')->where('product_id', $pid)->select()->toArray();
$skuMapAfter = [];
foreach ($skusAfter as $sku) {
    $skuMapAfter[$sku['sku_code']] = $sku['id'];
}

if ($skuMap['SKU_RED'] == $skuMapAfter['SKU_RED']) {
    echo "SUCCESS: SKU ID persisted for SKU_RED ({$skuMap['SKU_RED']}).\n";
} else {
    echo "FAILURE: SKU ID changed for SKU_RED! Old: {$skuMap['SKU_RED']}, New: {$skuMapAfter['SKU_RED']}\n";
}

// 5. Edit Product - Change Spec
echo "\n--- 4. Edit Product (Change Spec) ---\n";
// Change Blue to Green
$editParams['specs'][0]['values'] = ['Red', 'Green'];
$editParams['skus'][1] = [
    'value_names' => 'Green', // Changed from Blue
    'sku_code' => 'SKU_GREEN',
    'price' => 210,
    'stock' => 10,
    'image' => 'green.jpg',
    'market_price' => 230,
    'cost_price' => 60,
    'weight' => 1.5,
    'volume' => 0.02,
];

try {
    $result = ProductLogic::edit($editParams);
    if ($result) {
        echo "ProductLogic::edit (spec change) executed successfully.\n";
    } else {
        echo "ProductLogic::edit (spec change) failed. Error: " . ProductLogic::getError() . "\n";
        exit(1);
    }
} catch (\Throwable $e) {
    echo "ProductLogic::edit failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Verify
$skusFinal = Db::table('la_product_sku')->where('product_id', $pid)->select()->toArray();
$skuMapFinal = [];
foreach ($skusFinal as $sku) {
    $skuMapFinal[$sku['sku_code']] = $sku['id'];
}

if ($skuMap['SKU_RED'] == $skuMapFinal['SKU_RED']) {
    echo "SUCCESS: SKU ID persisted for SKU_RED after spec change.\n";
} else {
    echo "FAILURE: SKU ID changed for SKU_RED after spec change!\n";
}

if (isset($skuMapFinal['SKU_GREEN'])) {
    echo "SUCCESS: New SKU created for SKU_GREEN (ID: {$skuMapFinal['SKU_GREEN']}).\n";
} else {
    echo "FAILURE: SKU_GREEN not found!\n";
}

if (!isset($skuMapFinal['SKU_BLUE'])) {
    echo "SUCCESS: SKU_BLUE removed.\n";
} else {
    echo "FAILURE: SKU_BLUE still exists!\n";
}

// 6. Validation - Negative Price
echo "\n--- 5. Validation (Negative Price) ---\n";
$badParams = $params;
unset($badParams['id']); // New product
$badParams['name'] = "Bad Price Product";
$badParams['price'] = -100;

try {
    // Note: If Logic/Model doesn't validate, this might succeed or fail at DB level.
    // We expect it to fail if validation is in place.
    ProductLogic::add($badParams);
    echo "WARNING: Added product with negative price. Validation might be missing in Logic/Model layer.\n";
    cleanup("Bad Price Product");
} catch (\Throwable $e) {
    echo "SUCCESS: Expected error caught: " . $e->getMessage() . "\n";
}

echo "\n--- Test Complete ---\n";
