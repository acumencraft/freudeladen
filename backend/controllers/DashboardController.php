<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\AdminLog;
use common\models\Order;
use common\models\Product;
use common\models\User;

/**
 * DashboardController handles the admin dashboard
 */
class DashboardController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'stats'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays the dashboard
     *
     * @return string
     */
    public function actionIndex()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent orders
        $recentOrders = Order::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        // Get recent activity logs
        $recentLogs = AdminLog::find()
            ->with('user')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        // Log dashboard access
        AdminLog::log('view', 'dashboard');

        return $this->render('index', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentLogs' => $recentLogs,
        ]);
    }

    /**
     * Get dashboard statistics
     *
     * @return array
     */
    private function getDashboardStats()
    {
        // Today's date range
        $today = date('Y-m-d');
        $todayStart = $today . ' 00:00:00';
        $todayEnd = $today . ' 23:59:59';

        // This month's date range
        $monthStart = date('Y-m-01 00:00:00');
        $monthEnd = date('Y-m-t 23:59:59');

        // Get statistics
        $stats = [
            'totalOrders' => Order::find()->count(),
            'todayOrders' => Order::find()
                ->where(['between', 'created_at', $todayStart, $todayEnd])
                ->count(),
            'monthlyOrders' => Order::find()
                ->where(['between', 'created_at', $monthStart, $monthEnd])
                ->count(),
            'totalRevenue' => Order::find()
                ->where(['status' => 'completed'])
                ->sum('total') ?: 0,
            'monthlyRevenue' => Order::find()
                ->where(['status' => 'completed'])
                ->andWhere(['between', 'created_at', $monthStart, $monthEnd])
                ->sum('total') ?: 0,
            'totalProducts' => Product::find()->count(),
            'lowStockProducts' => Product::find()
                ->where(['<', 'stock', 10])
                ->count(),
            'totalUsers' => User::find()->count(),
            'newUsersToday' => User::find()
                ->where(['between', 'created_at', $todayStart, $todayEnd])
                ->count(),
            'pendingOrders' => Order::find()
                ->where(['status' => 'pending'])
                ->count(),
        ];

        return $stats;
    }

    /**
     * Ajax endpoint for dashboard stats
     *
     * @return \yii\web\Response
     */
    public function actionStats()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->getDashboardStats();
    }
}
