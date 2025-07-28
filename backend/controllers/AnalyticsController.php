<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use common\models\Transaction;
use common\models\PaymentMethod;
use common\models\Order;
use common\models\Product;
use common\models\BlogPost;
use common\models\User;

/**
 * AnalyticsController implements comprehensive analytics dashboard
 */
class AnalyticsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'refresh-data' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Main analytics dashboard
     * @return mixed
     */
    public function actionIndex()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01')); // First day of current month
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t')); // Last day of current month
        
        // Key Performance Indicators (KPIs)
        $kpis = $this->getKPIs($dateFrom, $dateTo);
        
        // Revenue analytics
        $revenueData = $this->getRevenueAnalytics($dateFrom, $dateTo);
        
        // Order analytics
        $orderData = $this->getOrderAnalytics($dateFrom, $dateTo);
        
        // Payment method analytics
        $paymentData = $this->getPaymentMethodAnalytics($dateFrom, $dateTo);
        
        // Product performance
        $productData = $this->getProductAnalytics($dateFrom, $dateTo);
        
        // User analytics
        $userData = $this->getUserAnalytics($dateFrom, $dateTo);
        
        // Content analytics
        $contentData = $this->getContentAnalytics($dateFrom, $dateTo);
        
        return $this->render('index', [
            'kpis' => $kpis,
            'revenueData' => $revenueData,
            'orderData' => $orderData,
            'paymentData' => $paymentData,
            'productData' => $productData,
            'userData' => $userData,
            'contentData' => $contentData,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Sales analytics page
     * @return mixed
     */
    public function actionSales()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));
        
        // Daily sales data for charts
        $dailySales = $this->getDailySalesData($dateFrom, $dateTo);
        
        // Sales by payment method
        $paymentMethodSales = $this->getSalesByPaymentMethod($dateFrom, $dateTo);
        
        // Sales by product category
        $categorySales = $this->getSalesByCategory($dateFrom, $dateTo);
        
        // Top selling products
        $topProducts = $this->getTopSellingProducts($dateFrom, $dateTo);
        
        // Sales funnel analysis
        $salesFunnel = $this->getSalesFunnelData($dateFrom, $dateTo);
        
        return $this->render('sales', [
            'dailySales' => $dailySales,
            'paymentMethodSales' => $paymentMethodSales,
            'categorySales' => $categorySales,
            'topProducts' => $topProducts,
            'salesFunnel' => $salesFunnel,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Payment analytics page
     * @return mixed
     */
    public function actionPayments()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));
        
        // Payment method performance
        $methodPerformance = $this->getPaymentMethodPerformance($dateFrom, $dateTo);
        
        // Transaction success rates
        $successRates = $this->getTransactionSuccessRates($dateFrom, $dateTo);
        
        // Failed transaction analysis
        $failedTransactions = $this->getFailedTransactionAnalysis($dateFrom, $dateTo);
        
        // Fee analytics
        $feeAnalytics = $this->getFeeAnalytics($dateFrom, $dateTo);
        
        // Payment trends
        $paymentTrends = $this->getPaymentTrends($dateFrom, $dateTo);
        
        return $this->render('payments', [
            'methodPerformance' => $methodPerformance,
            'successRates' => $successRates,
            'failedTransactions' => $failedTransactions,
            'feeAnalytics' => $feeAnalytics,
            'paymentTrends' => $paymentTrends,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * User analytics page
     * @return mixed
     */
    public function actionUsers()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));
        
        // User acquisition data
        $userAcquisition = $this->getUserAcquisitionData($dateFrom, $dateTo);
        
        // User retention analysis
        $userRetention = $this->getUserRetentionData($dateFrom, $dateTo);
        
        // Customer lifetime value
        $lifetimeValue = $this->getCustomerLifetimeValue($dateFrom, $dateTo);
        
        // User behavior analytics
        $userBehavior = $this->getUserBehaviorData($dateFrom, $dateTo);
        
        return $this->render('users', [
            'userAcquisition' => $userAcquisition,
            'userRetention' => $userRetention,
            'lifetimeValue' => $lifetimeValue,
            'userBehavior' => $userBehavior,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Get Key Performance Indicators
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getKPIs($dateFrom, $dateTo)
    {
        // Current period data
        $currentRevenue = Transaction::find()
            ->where(['status' => Transaction::STATUS_COMPLETED])
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->sum('amount') ?: 0;
        
        $currentOrders = Transaction::find()
            ->where(['status' => Transaction::STATUS_COMPLETED, 'type' => Transaction::TYPE_PAYMENT])
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        $currentUsers = User::find()
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        // Previous period for comparison
        $daysDiff = (strtotime($dateTo) - strtotime($dateFrom)) / 86400;
        $prevDateFrom = date('Y-m-d', strtotime($dateFrom . ' -' . $daysDiff . ' days'));
        $prevDateTo = date('Y-m-d', strtotime($dateFrom . ' -1 day'));
        
        $previousRevenue = Transaction::find()
            ->where(['status' => Transaction::STATUS_COMPLETED])
            ->andWhere(['between', 'created_at', $prevDateFrom . ' 00:00:00', $prevDateTo . ' 23:59:59'])
            ->sum('amount') ?: 0;
        
        $previousOrders = Transaction::find()
            ->where(['status' => Transaction::STATUS_COMPLETED, 'type' => Transaction::TYPE_PAYMENT])
            ->andWhere(['between', 'created_at', $prevDateFrom . ' 00:00:00', $prevDateTo . ' 23:59:59'])
            ->count();
        
        return [
            'revenue' => [
                'current' => $currentRevenue,
                'previous' => $previousRevenue,
                'change' => $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue * 100) : 0,
            ],
            'orders' => [
                'current' => $currentOrders,
                'previous' => $previousOrders,
                'change' => $previousOrders > 0 ? (($currentOrders - $previousOrders) / $previousOrders * 100) : 0,
            ],
            'users' => [
                'current' => $currentUsers,
                'change' => 0, // Would need user registration tracking
            ],
            'avgOrderValue' => [
                'current' => $currentOrders > 0 ? $currentRevenue / $currentOrders : 0,
                'previous' => $previousOrders > 0 ? $previousRevenue / $previousOrders : 0,
            ],
        ];
    }

    /**
     * Get revenue analytics data
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getRevenueAnalytics($dateFrom, $dateTo)
    {
        // Daily revenue data
        $dailyRevenue = [];
        $period = new \DatePeriod(
            new \DateTime($dateFrom),
            new \DateInterval('P1D'),
            new \DateTime($dateTo . ' +1 day')
        );
        
        foreach ($period as $date) {
            $dayStr = $date->format('Y-m-d');
            $revenue = Transaction::find()
                ->where(['status' => Transaction::STATUS_COMPLETED])
                ->andWhere(['between', 'created_at', $dayStr . ' 00:00:00', $dayStr . ' 23:59:59'])
                ->sum('amount') ?: 0;
            
            $dailyRevenue[] = [
                'date' => $dayStr,
                'revenue' => $revenue,
            ];
        }
        
        return [
            'daily' => $dailyRevenue,
            'total' => array_sum(array_column($dailyRevenue, 'revenue')),
            'average' => count($dailyRevenue) > 0 ? array_sum(array_column($dailyRevenue, 'revenue')) / count($dailyRevenue) : 0,
        ];
    }

    /**
     * Get order analytics data
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getOrderAnalytics($dateFrom, $dateTo)
    {
        $totalOrders = Transaction::find()
            ->where(['status' => Transaction::STATUS_COMPLETED, 'type' => Transaction::TYPE_PAYMENT])
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        $completedOrders = Transaction::find()
            ->where(['status' => Transaction::STATUS_COMPLETED])
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        $pendingOrders = Transaction::find()
            ->where(['status' => Transaction::STATUS_PENDING])
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        return [
            'total' => $totalOrders,
            'completed' => $completedOrders,
            'pending' => $pendingOrders,
            'completionRate' => $totalOrders > 0 ? ($completedOrders / $totalOrders * 100) : 0,
        ];
    }

    /**
     * Get payment method analytics
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getPaymentMethodAnalytics($dateFrom, $dateTo)
    {
        $methods = PaymentMethod::find()->all();
        $analytics = [];
        
        foreach ($methods as $method) {
            $transactions = Transaction::find()
                ->where(['payment_method_id' => $method->id, 'status' => Transaction::STATUS_COMPLETED])
                ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            
            $count = $transactions->count();
            $amount = $transactions->sum('amount') ?: 0;
            $fees = $transactions->sum('fee') ?: 0;
            
            $analytics[] = [
                'method' => $method,
                'transactions' => $count,
                'amount' => $amount,
                'fees' => $fees,
                'avgAmount' => $count > 0 ? $amount / $count : 0,
            ];
        }
        
        // Sort by transaction count
        usort($analytics, function($a, $b) {
            return $b['transactions'] - $a['transactions'];
        });
        
        return $analytics;
    }

    /**
     * Get product analytics (placeholder for when product-order relation exists)
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getProductAnalytics($dateFrom, $dateTo)
    {
        // This would require order_item table to track product sales
        // For now, return basic product counts
        
        $totalProducts = Product::find()->count();
        $activeProducts = Product::find()->where(['status' => 1])->count();
        
        return [
            'total' => $totalProducts,
            'active' => $activeProducts,
            'topSelling' => [], // Would be populated with actual sales data
        ];
    }

    /**
     * Get user analytics data
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getUserAnalytics($dateFrom, $dateTo)
    {
        $newUsers = User::find()
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        $totalUsers = User::find()->count();
        
        return [
            'new' => $newUsers,
            'total' => $totalUsers,
            'growth' => 0, // Would calculate based on previous period
        ];
    }

    /**
     * Get content analytics data
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getContentAnalytics($dateFrom, $dateTo)
    {
        $newPosts = BlogPost::find()
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->count();
        
        $totalPosts = BlogPost::find()->count();
        $publishedPosts = BlogPost::find()->where(['status' => BlogPost::STATUS_PUBLISHED])->count();
        
        return [
            'newPosts' => $newPosts,
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
        ];
    }

    /**
     * Get daily sales data for charts
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getDailySalesData($dateFrom, $dateTo)
    {
        $data = [];
        $period = new \DatePeriod(
            new \DateTime($dateFrom),
            new \DateInterval('P1D'),
            new \DateTime($dateTo . ' +1 day')
        );
        
        foreach ($period as $date) {
            $dayStr = $date->format('Y-m-d');
            
            $orders = Transaction::find()
                ->where(['status' => Transaction::STATUS_COMPLETED, 'type' => Transaction::TYPE_PAYMENT])
                ->andWhere(['between', 'created_at', $dayStr . ' 00:00:00', $dayStr . ' 23:59:59'])
                ->count();
                
            $revenue = Transaction::find()
                ->where(['status' => Transaction::STATUS_COMPLETED])
                ->andWhere(['between', 'created_at', $dayStr . ' 00:00:00', $dayStr . ' 23:59:59'])
                ->sum('amount') ?: 0;
            
            $data[] = [
                'date' => $dayStr,
                'orders' => $orders,
                'revenue' => $revenue,
            ];
        }
        
        return $data;
    }

    /**
     * Get sales by payment method
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getSalesByPaymentMethod($dateFrom, $dateTo)
    {
        $data = [];
        $methods = PaymentMethod::find()->all();
        
        foreach ($methods as $method) {
            $revenue = Transaction::find()
                ->where(['payment_method_id' => $method->id, 'status' => Transaction::STATUS_COMPLETED])
                ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->sum('amount') ?: 0;
            
            if ($revenue > 0) {
                $data[] = [
                    'method' => $method->name,
                    'revenue' => $revenue,
                ];
            }
        }
        
        return $data;
    }

    /**
     * Get sales by category (placeholder)
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getSalesByCategory($dateFrom, $dateTo)
    {
        // Would require order-product-category relationship
        return [];
    }

    /**
     * Get top selling products (placeholder)
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getTopSellingProducts($dateFrom, $dateTo)
    {
        // Would require order-product relationship
        return [];
    }

    /**
     * Get sales funnel data (placeholder)
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getSalesFunnelData($dateFrom, $dateTo)
    {
        return [
            'visitors' => 0, // Would track from analytics
            'views' => 0,
            'addToCart' => 0,
            'checkout' => 0,
            'completed' => Transaction::find()
                ->where(['status' => Transaction::STATUS_COMPLETED])
                ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count(),
        ];
    }

    /**
     * Get payment method performance
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getPaymentMethodPerformance($dateFrom, $dateTo)
    {
        return $this->getPaymentMethodAnalytics($dateFrom, $dateTo);
    }

    /**
     * Get transaction success rates
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getTransactionSuccessRates($dateFrom, $dateTo)
    {
        $methods = PaymentMethod::find()->all();
        $rates = [];
        
        foreach ($methods as $method) {
            $total = Transaction::find()
                ->where(['payment_method_id' => $method->id])
                ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count();
            
            $successful = Transaction::find()
                ->where(['payment_method_id' => $method->id, 'status' => Transaction::STATUS_COMPLETED])
                ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count();
            
            $rates[] = [
                'method' => $method->name,
                'total' => $total,
                'successful' => $successful,
                'rate' => $total > 0 ? ($successful / $total * 100) : 0,
            ];
        }
        
        return $rates;
    }

    /**
     * Get failed transaction analysis
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getFailedTransactionAnalysis($dateFrom, $dateTo)
    {
        $failedTransactions = Transaction::find()
            ->where(['status' => Transaction::STATUS_FAILED])
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->with(['paymentMethod'])
            ->all();
        
        $analysis = [];
        foreach ($failedTransactions as $transaction) {
            $methodName = $transaction->paymentMethod ? $transaction->paymentMethod->name : 'Unknown';
            if (!isset($analysis[$methodName])) {
                $analysis[$methodName] = ['count' => 0, 'amount' => 0];
            }
            $analysis[$methodName]['count']++;
            $analysis[$methodName]['amount'] += $transaction->amount;
        }
        
        return $analysis;
    }

    /**
     * Get fee analytics
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getFeeAnalytics($dateFrom, $dateTo)
    {
        $totalFees = Transaction::find()
            ->where(['status' => Transaction::STATUS_COMPLETED])
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->sum('fee') ?: 0;
        
        $totalRevenue = Transaction::find()
            ->where(['status' => Transaction::STATUS_COMPLETED])
            ->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->sum('amount') ?: 0;
        
        return [
            'totalFees' => $totalFees,
            'totalRevenue' => $totalRevenue,
            'feePercentage' => $totalRevenue > 0 ? ($totalFees / $totalRevenue * 100) : 0,
        ];
    }

    /**
     * Get payment trends
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getPaymentTrends($dateFrom, $dateTo)
    {
        // Daily payment volume trends
        $trends = [];
        $period = new \DatePeriod(
            new \DateTime($dateFrom),
            new \DateInterval('P1D'),
            new \DateTime($dateTo . ' +1 day')
        );
        
        foreach ($period as $date) {
            $dayStr = $date->format('Y-m-d');
            
            $volume = Transaction::find()
                ->where(['status' => Transaction::STATUS_COMPLETED])
                ->andWhere(['between', 'created_at', $dayStr . ' 00:00:00', $dayStr . ' 23:59:59'])
                ->sum('amount') ?: 0;
            
            $trends[] = [
                'date' => $dayStr,
                'volume' => $volume,
            ];
        }
        
        return $trends;
    }

    /**
     * Get user acquisition data (placeholder)
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getUserAcquisitionData($dateFrom, $dateTo)
    {
        return [
            'organic' => 0,
            'referral' => 0,
            'social' => 0,
            'direct' => 0,
        ];
    }

    /**
     * Get user retention data (placeholder)
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getUserRetentionData($dateFrom, $dateTo)
    {
        return [
            'day1' => 0,
            'day7' => 0,
            'day30' => 0,
        ];
    }

    /**
     * Get customer lifetime value (placeholder)
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getCustomerLifetimeValue($dateFrom, $dateTo)
    {
        return [
            'average' => 0,
            'median' => 0,
            'top10percent' => 0,
        ];
    }

    /**
     * Get user behavior data (placeholder)
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    private function getUserBehaviorData($dateFrom, $dateTo)
    {
        return [
            'pageViews' => 0,
            'sessionDuration' => 0,
            'bounceRate' => 0,
        ];
    }

    /**
     * Generate comprehensive analytics report
     */
    public function actionGenerateReport($date_from = null, $date_to = null, $format = 'html')
    {
        if ($date_from === null) {
            $date_from = date('Y-m-d', strtotime('-30 days'));
        }
        if ($date_to === null) {
            $date_to = date('Y-m-d');
        }

        // Get all analytics data
        $kpis = $this->getKPIs($date_from, $date_to);
        $revenueData = $this->getRevenueAnalytics($date_from, $date_to);
        $paymentData = $this->getPaymentAnalytics($date_from, $date_to);
        $userData = $this->getUserAnalytics($date_from, $date_to);
        $salesData = $this->getSalesAnalytics($date_from, $date_to);

        if ($format === 'pdf') {
            // TODO: Implement PDF generation
            Yii::$app->session->setFlash('info', 'PDF export will be implemented in the next version.');
            return $this->redirect(['index']);
        }

        if ($format === 'excel') {
            // TODO: Implement Excel generation
            Yii::$app->session->setFlash('info', 'Excel export will be implemented in the next version.');
            return $this->redirect(['index']);
        }

        return $this->render('report', [
            'dateFrom' => $date_from,
            'dateTo' => $date_to,
            'kpis' => $kpis,
            'revenueData' => $revenueData,
            'paymentData' => $paymentData,
            'userData' => $userData,
            'salesData' => $salesData,
        ]);
    }

    /**
     * Export analytics data
     */
    public function actionExport($type = 'sales', $date_from = null, $date_to = null)
    {
        if ($date_from === null) {
            $date_from = date('Y-m-d', strtotime('-30 days'));
        }
        if ($date_to === null) {
            $date_to = date('Y-m-d');
        }

        $filename = $type . '_analytics_' . $date_from . '_to_' . $date_to . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        switch ($type) {
            case 'sales':
                $data = $this->getSalesAnalytics($date_from, $date_to);
                fputcsv($output, ['Date', 'Orders', 'Revenue', 'Items Sold']);
                foreach ($data['dailyStats'] as $day) {
                    fputcsv($output, [
                        $day['date'],
                        $day['orders'],
                        $day['revenue'],
                        $day['items']
                    ]);
                }
                break;
                
            case 'payments':
                $data = $this->getPaymentAnalytics($date_from, $date_to);
                fputcsv($output, ['Payment Method', 'Transactions', 'Amount', 'Fees']);
                foreach ($data['paymentMethodStats'] as $stats) {
                    fputcsv($output, [
                        $stats['method']->name,
                        $stats['transactions'],
                        $stats['amount'],
                        $stats['fees']
                    ]);
                }
                break;
                
            case 'users':
                $data = $this->getUserAnalytics($date_from, $date_to);
                fputcsv($output, ['Date', 'New Registrations']);
                foreach ($data['dailyRegistrations'] as $day) {
                    fputcsv($output, [
                        $day['date'],
                        $day['registrations']
                    ]);
                }
                break;
        }
        
        fclose($output);
        exit;
    }
}
