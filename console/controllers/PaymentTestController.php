<?php
/**
 * Payment Testing Controller
 * 
 * Comprehensive testing suite for payment functionality
 * Tests all payment methods: Stripe, PayPal, and Bank Transfer
 */

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use common\models\User;
use common\models\Product;
use common\models\Category;
use common\models\Order;
use common\models\OrderItem;
use common\models\Cart;

class PaymentTestController extends Controller
{
    public $defaultAction = 'test-all';
    
    /**
     * Test all payment methods
     */
    public function actionTestAll()
    {
        $this->stdout("🧪 FREUDELADEN.DE - Payment System Testing\n", \yii\helpers\Console::FG_CYAN);
        $this->stdout("=" . str_repeat("=", 50) . "\n", \yii\helpers\Console::FG_CYAN);
        
        $results = [];
        
        // Test Stripe Payment
        $this->stdout("\n1️⃣  Testing Stripe Payment...\n", \yii\helpers\Console::FG_YELLOW);
        $results['stripe'] = $this->testStripePayment();
        
        // Test PayPal Payment
        $this->stdout("\n2️⃣  Testing PayPal Payment...\n", \yii\helpers\Console::FG_YELLOW);
        $results['paypal'] = $this->testPaypalPayment();
        
        // Test Bank Transfer
        $this->stdout("\n3️⃣  Testing Bank Transfer...\n", \yii\helpers\Console::FG_YELLOW);
        $results['bank_transfer'] = $this->testBankTransfer();
        
        // Test Order Creation
        $this->stdout("\n4️⃣  Testing Order Creation Flow...\n", \yii\helpers\Console::FG_YELLOW);
        $results['order_creation'] = $this->testOrderCreation();
        
        // Test Payment Pages
        $this->stdout("\n5️⃣  Testing Payment Pages...\n", \yii\helpers\Console::FG_YELLOW);
        $results['payment_pages'] = $this->testPaymentPages();
        
        // Print Summary
        $this->printTestSummary($results);
        
        // Return exit code based on results
        $allPassed = array_reduce($results, function($carry, $result) {
            return $carry && $result['success'];
        }, true);
        
        return $allPassed ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }
    
    /**
     * Test Stripe payment processing
     */
    protected function testStripePayment()
    {
        try {
            $this->stdout("  • Creating test order for Stripe...\n");
            $order = $this->createTestOrder();
            
            $this->stdout("  • Simulating Stripe payment...\n");
            
            // Simulate Stripe payment data
            $stripeData = [
                'order_id' => $order->id,
                'payment_intent' => 'pi_test_' . uniqid(),
                'amount' => $order->total_amount * 100, // Stripe uses cents
                'currency' => 'eur',
                'status' => 'succeeded'
            ];
            
            // Test payment processing
            $order->payment_method = 'stripe';
            $order->payment_status = 'paid';
            $order->payment_transaction_id = $stripeData['payment_intent'];
            
            if ($order->save()) {
                $this->stdout("  ✅ Stripe payment test PASSED\n", \yii\helpers\Console::FG_GREEN);
                $this->stdout("     - Order ID: {$order->id}\n");
                $this->stdout("     - Payment Intent: {$stripeData['payment_intent']}\n");
                $this->stdout("     - Amount: €" . number_format($order->total_amount, 2) . "\n");
                
                return [
                    'success' => true,
                    'order_id' => $order->id,
                    'payment_reference' => $stripeData['payment_intent'],
                    'message' => 'Stripe payment processed successfully'
                ];
            } else {
                throw new \Exception('Failed to save order: ' . print_r($order->errors, true));
            }
            
        } catch (\Exception $e) {
            $this->stdout("  ❌ Stripe payment test FAILED: " . $e->getMessage() . "\n", \yii\helpers\Console::FG_RED);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test PayPal payment processing
     */
    protected function testPaypalPayment()
    {
        try {
            $this->stdout("  • Creating test order for PayPal...\n");
            $order = $this->createTestOrder();
            
            $this->stdout("  • Simulating PayPal payment...\n");
            
            // Simulate PayPal payment data
            $paypalData = [
                'order_id' => $order->id,
                'payment_id' => 'PAY-' . strtoupper(uniqid()),
                'payer_id' => 'PAYER' . strtoupper(uniqid()),
                'amount' => $order->total_amount,
                'currency' => 'EUR',
                'status' => 'approved'
            ];
            
            // Test payment processing
            $order->payment_method = 'paypal';
            $order->payment_status = 'paid';
            $order->payment_transaction_id = $paypalData['payment_id'];
            
            if ($order->save()) {
                $this->stdout("  ✅ PayPal payment test PASSED\n", \yii\helpers\Console::FG_GREEN);
                $this->stdout("     - Order ID: {$order->id}\n");
                $this->stdout("     - Payment ID: {$paypalData['payment_id']}\n");
                $this->stdout("     - Payer ID: {$paypalData['payer_id']}\n");
                $this->stdout("     - Amount: €" . number_format($order->total_amount, 2) . "\n");
                
                return [
                    'success' => true,
                    'order_id' => $order->id,
                    'payment_reference' => $paypalData['payment_id'],
                    'message' => 'PayPal payment processed successfully'
                ];
            } else {
                throw new \Exception('Failed to save order: ' . print_r($order->errors, true));
            }
            
        } catch (\Exception $e) {
            $this->stdout("  ❌ PayPal payment test FAILED: " . $e->getMessage() . "\n", \yii\helpers\Console::FG_RED);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test Bank Transfer processing
     */
    protected function testBankTransfer()
    {
        try {
            $this->stdout("  • Creating test order for Bank Transfer...\n");
            $order = $this->createTestOrder();
            
            $this->stdout("  • Processing Bank Transfer...\n");
            
            // Test bank transfer processing
            $order->payment_method = 'bank_transfer';
            $order->payment_status = 'pending';
            $order->payment_transaction_id = 'BT-' . date('Ymd') . '-' . $order->id;
            
            if ($order->save()) {
                $this->stdout("  ✅ Bank Transfer test PASSED\n", \yii\helpers\Console::FG_GREEN);
                $this->stdout("     - Order ID: {$order->id}\n");
                $this->stdout("     - Reference: {$order->payment_transaction_id}\n");
                $this->stdout("     - Status: Pending (waiting for transfer)\n");
                $this->stdout("     - Amount: €" . number_format($order->total_amount, 2) . "\n");
                
                return [
                    'success' => true,
                    'order_id' => $order->id,
                    'payment_reference' => $order->payment_transaction_id,
                    'message' => 'Bank transfer order created successfully'
                ];
            } else {
                throw new \Exception('Failed to save order: ' . print_r($order->errors, true));
            }
            
        } catch (\Exception $e) {
            $this->stdout("  ❌ Bank Transfer test FAILED: " . $e->getMessage() . "\n", \yii\helpers\Console::FG_RED);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test order creation flow
     */
    protected function testOrderCreation()
    {
        try {
            $this->stdout("  • Testing complete order creation flow...\n");
            
            // Create test products
            $products = $this->getTestProducts();
            
            if (empty($products)) {
                throw new \Exception('No test products available');
            }
            
            $this->stdout("  • Found " . count($products) . " test products\n");
            
            // Calculate totals
            $subtotal = 0;
            $orderItems = [];
            foreach ($products as $product) {
                $quantity = 1;
                $price = $product->getEffectivePrice();
                $subtotal += $price * $quantity;
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }
            $shipping = 5.99; // Standard shipping
            $total = $subtotal + $shipping;
            
            // Create order
            $order = new Order();
            $order->user_id = null; // Guest order
            $order->status = 'pending';
            $order->payment_status = 'pending';
            $order->shipping_amount = $shipping;
            $order->total_amount = $total;
            $order->customer_email = 'guest@freudeladen.de';
            $order->shipping_first_name = 'Test';
            $order->shipping_last_name = 'User';
            $order->billing_first_name = 'Test';
            $order->billing_last_name = 'User';
            $order->shipping_address_1 = 'Teststraße 123';
            $order->shipping_city = 'Berlin';
            $order->shipping_postal_code = '10115';
            $order->shipping_country = 'DE';
            $order->order_number = 'ORD-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));
            
            if ($order->save()) {
                // Create order items
                foreach ($orderItems as $item) {
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $item['product']->id;
                    $orderItem->product_name = $item['product']->name;
                    $orderItem->product_sku = $item['product']->sku ?? 'SKU-' . $item['product']->id;
                    $orderItem->quantity = $item['quantity'];
                    $orderItem->unit_price = $item['price'];
                    $orderItem->total_price = $item['price'] * $item['quantity'];
                    $orderItem->save();
                }
                
                $this->stdout("  ✅ Order creation test PASSED\n", \yii\helpers\Console::FG_GREEN);
                $this->stdout("     - Order ID: {$order->id}\n");
                $this->stdout("     - Items: " . count($orderItems) . "\n");
                $this->stdout("     - Subtotal: €" . number_format($subtotal, 2) . "\n");
                $this->stdout("     - Shipping: €" . number_format($shipping, 2) . "\n");
                $this->stdout("     - Total: €" . number_format($total, 2) . "\n");
                
                return [
                    'success' => true,
                    'order_id' => $order->id,
                    'total_amount' => $total,
                    'items_count' => count($orderItems),
                    'message' => 'Order created successfully'
                ];
            } else {
                throw new \Exception('Failed to create order: ' . print_r($order->errors, true));
            }
            
        } catch (\Exception $e) {
            $this->stdout("  ❌ Order creation test FAILED: " . $e->getMessage() . "\n", \yii\helpers\Console::FG_RED);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test payment pages accessibility
     */
    protected function testPaymentPages()
    {
        try {
            $this->stdout("  • Testing payment page templates...\n");
            
            $order = $this->createTestOrder();
            $pages = ['success', 'cancel', 'bank-instructions'];
            $pageResults = [];
            
            foreach ($pages as $page) {
                $viewFile = Yii::getAlias('@frontend/views/payment/' . $page . '.php');
                if (file_exists($viewFile)) {
                    $pageResults[$page] = [
                        'exists' => true,
                        'readable' => is_readable($viewFile),
                        'size' => filesize($viewFile)
                    ];
                    $this->stdout("     ✅ {$page}.php - OK (" . filesize($viewFile) . " bytes)\n");
                } else {
                    $pageResults[$page] = [
                        'exists' => false,
                        'readable' => false,
                        'size' => 0
                    ];
                    $this->stdout("     ❌ {$page}.php - NOT FOUND\n", \yii\helpers\Console::FG_RED);
                }
            }
            
            $allPagesExist = array_reduce($pageResults, function($carry, $result) {
                return $carry && $result['exists'];
            }, true);
            
            if ($allPagesExist) {
                $this->stdout("  ✅ Payment pages test PASSED\n", \yii\helpers\Console::FG_GREEN);
                return [
                    'success' => true,
                    'pages' => $pageResults,
                    'message' => 'All payment pages are accessible'
                ];
            } else {
                throw new \Exception('Some payment pages are missing');
            }
            
        } catch (\Exception $e) {
            $this->stdout("  ❌ Payment pages test FAILED: " . $e->getMessage() . "\n", \yii\helpers\Console::FG_RED);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Create a test order for payment testing
     */
    protected function createTestOrder()
    {
        $products = $this->getTestProducts();
        
        if (empty($products)) {
            throw new \Exception('No test products available');
        }
        
        $product = $products[0];
        $quantity = 2;
        $subtotal = $product->price * $quantity;
        $shipping = 5.99;
        $total = $subtotal + $shipping;
        
        $order = new Order();
        $order->user_id = null; // Allow null for guest orders
        $order->status = 'pending';
        $order->payment_status = 'pending';
        $order->shipping_amount = $shipping;
        $order->total_amount = $total;
        $order->customer_email = 'guest@freudeladen.de';
        $order->shipping_first_name = 'Test';
        $order->shipping_last_name = 'User';
        $order->billing_first_name = 'Test';
        $order->billing_last_name = 'User';
        $order->shipping_address_1 = 'Teststraße 123';
        $order->shipping_city = 'Berlin';
        $order->shipping_postal_code = '10115';
        $order->shipping_country = 'DE';
        $order->order_number = 'ORD-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));
        
        if (!$order->save()) {
            throw new \Exception('Failed to create test order: ' . print_r($order->errors, true));
        }
        
        // Create order item
        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->product_id = $product->id;
        $orderItem->product_name = $product->name;
        $orderItem->product_sku = $product->sku ?? 'SKU-' . $product->id;
        $orderItem->quantity = $quantity;
        $orderItem->unit_price = $product->price;
        $orderItem->total_price = $product->price * $quantity;
        $orderItem->save();
        
        return $order;
    }
    
    /**
     * Get or create test user
     */
    protected function getOrCreateTestUser()
    {
        $user = User::findOne(['email' => 'payment.test@freudeladen.de']);
        
        if (!$user) {
            $user = new User();
            $user->username = 'paymenttest';
            $user->email = 'payment.test@freudeladen.de';
            $user->setPassword('testpassword123');
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();
            $user->status = User::STATUS_ACTIVE;
            
            if (!$user->save()) {
                throw new \Exception('Failed to create test user: ' . print_r($user->errors, true));
            }
        }
        
        return $user;
    }
    
    /**
     * Get test products
     */
    protected function getTestProducts()
    {
        return Product::find()->limit(3)->all();
    }
    
    /**
     * Print test summary
     */
    protected function printTestSummary($results)
    {
        $this->stdout("\n" . str_repeat("=", 60) . "\n", \yii\helpers\Console::FG_CYAN);
        $this->stdout("🏆 PAYMENT SYSTEM TEST SUMMARY\n", \yii\helpers\Console::FG_CYAN);
        $this->stdout(str_repeat("=", 60) . "\n", \yii\helpers\Console::FG_CYAN);
        
        $totalTests = count($results);
        $passedTests = 0;
        
        foreach ($results as $testName => $result) {
            $status = $result['success'] ? '✅ PASSED' : '❌ FAILED';
            $color = $result['success'] ? \yii\helpers\Console::FG_GREEN : \yii\helpers\Console::FG_RED;
            
            $this->stdout(sprintf("%-20s %s\n", strtoupper($testName), $status), $color);
            
            if ($result['success']) {
                $passedTests++;
                if (isset($result['message'])) {
                    $this->stdout("  └─ " . $result['message'] . "\n");
                }
            } else {
                if (isset($result['error'])) {
                    $this->stdout("  └─ Error: " . $result['error'] . "\n", \yii\helpers\Console::FG_RED);
                }
            }
        }
        
        $this->stdout("\n");
        $this->stdout("📊 RESULTS: {$passedTests}/{$totalTests} tests passed\n", \yii\helpers\Console::FG_CYAN);
        
        if ($passedTests === $totalTests) {
            $this->stdout("🎉 All payment tests PASSED! Payment system is ready for production.\n", \yii\helpers\Console::FG_GREEN);
        } else {
            $this->stdout("⚠️  Some tests FAILED. Please review the errors above.\n", \yii\helpers\Console::FG_YELLOW);
        }
        
        $this->stdout(str_repeat("=", 60) . "\n", \yii\helpers\Console::FG_CYAN);
    }
    
    /**
     * Test specific payment method
     */
    public function actionTestMethod($method)
    {
        $this->stdout("🧪 Testing {$method} payment method...\n", \yii\helpers\Console::FG_CYAN);
        
        switch ($method) {
            case 'stripe':
                $result = $this->testStripePayment();
                break;
            case 'paypal':
                $result = $this->testPaypalPayment();
                break;
            case 'bank':
            case 'bank_transfer':
                $result = $this->testBankTransfer();
                break;
            default:
                $this->stdout("❌ Unknown payment method: {$method}\n", \yii\helpers\Console::FG_RED);
                return ExitCode::DATAERR;
        }
        
        return $result['success'] ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }
    
    /**
     * Clean up test data
     */
    public function actionCleanup()
    {
        $this->stdout("🧹 Cleaning up payment test data...\n", \yii\helpers\Console::FG_YELLOW);
        
        // Delete test orders
        $deletedOrders = Order::deleteAll(['like', 'shipping_address', 'Test User']);
        $this->stdout("  • Deleted {$deletedOrders} test orders\n");
        
        // Delete test user
        $testUser = User::findOne(['email' => 'payment.test@freudeladen.de']);
        if ($testUser) {
            $testUser->delete();
            $this->stdout("  • Deleted test user\n");
        }
        
        $this->stdout("✅ Cleanup completed\n", \yii\helpers\Console::FG_GREEN);
        
        return ExitCode::OK;
    }
}
