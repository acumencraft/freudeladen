<?php

require_once __DIR__ . '/vendor/autoload.php';

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require_once __DIR__ . '/common/config/bootstrap.php';
require_once __DIR__ . '/console/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/console/config/main.php',
    require __DIR__ . '/console/config/main-local.php'
);

$app = new yii\console\Application($config);

// Test Order model
$order = new common\models\Order();

echo "Order class: " . get_class($order) . "\n";

// Test method name conversion
$name = 'customer_name';
$camelName = str_replace('_', '', ucwords($name, '_'));
$setterMethod = 'set' . $camelName;
echo "Original: '$name'\n";
echo "CamelCase: '$camelName'\n";
echo "Setter method: '$setterMethod'\n";
echo "Method exists: " . (method_exists($order, $setterMethod) ? 'yes' : 'no') . "\n";

// Test property access now
try {
    $order->customer_name = 'Test User Property';
    echo "Set customer_name: ok\n";
    echo "Get customer_name: " . $order->customer_name . "\n";
} catch (Exception $e) {
    echo "Property access error: " . $e->getMessage() . "\n";
}
