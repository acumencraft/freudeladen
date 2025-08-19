<?php
// Test script for Payment Management System
define('YII_DEBUG', true);
define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/backend/config/bootstrap.php';

use common\models\PaymentMethod;
use common\models\Transaction;

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/console/config/main.php',
    require __DIR__ . '/console/config/main-local.php'
);

$application = new yii\console\Application($config);

echo "=== Payment Management System Tests ===\n\n";

// Test 1: PaymentMethod fee calculation
echo "Test 1: Payment Method Fee Calculation\n";
$paymentMethod = PaymentMethod::findOne(1); // Credit Card Stripe
if ($paymentMethod) {
    echo "Payment Method: {$paymentMethod->name}\n";
    echo "Fee Type: {$paymentMethod->getFeeTypeLabel()}\n";
    
    $testAmount = 100.00;
    $calculatedFee = $paymentMethod->calculateFee($testAmount);
    echo "Amount: €{$testAmount} -> Fee: €{$calculatedFee}\n";
    
    echo "Available for €50: " . ($paymentMethod->isAvailableForAmount(50) ? 'Yes' : 'No') . "\n";
    echo "Available for €15000: " . ($paymentMethod->isAvailableForAmount(15000) ? 'Yes' : 'No') . "\n";
}

echo "\n";

// Test 2: Transaction statistics
echo "Test 2: Transaction Statistics\n";
$totalTransactions = Transaction::find()->count();
$completedTransactions = Transaction::find()->where(['status' => Transaction::STATUS_COMPLETED])->count();
$pendingTransactions = Transaction::find()->where(['status' => Transaction::STATUS_PENDING])->count();
$totalAmount = Transaction::find()->where(['status' => Transaction::STATUS_COMPLETED])->sum('amount');

echo "Total Transactions: {$totalTransactions}\n";
echo "Completed: {$completedTransactions}\n";
echo "Pending: {$pendingTransactions}\n";
echo "Total Amount: €{$totalAmount}\n";

echo "\n";

// Test 3: Active payment methods
echo "Test 3: Active Payment Methods\n";
$activeMethods = PaymentMethod::getActive()->all();
echo "Active Payment Methods: " . count($activeMethods) . "\n";
foreach ($activeMethods as $method) {
    echo "- {$method->name} ({$method->getTypeLabel()})\n";
}

echo "\n";

// Test 4: Transaction by payment method
echo "Test 4: Transactions by Payment Method\n";
foreach ($activeMethods as $method) {
    $transactionCount = $method->getTransactionCount();
    $totalAmount = $method->getTotalAmount();
    echo "- {$method->name}: {$transactionCount} transactions, €{$totalAmount} total\n";
}

echo "\n=== All Tests Completed Successfully! ===\n";
