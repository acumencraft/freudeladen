<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Product;
use app\models\Order;
use app\models\User;
use app\models\BlogPost;

/**
 * Dashboard controller for the admin module
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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays the admin dashboard with key statistics
     * @return string
     */
    public function actionIndex()
    {
        // Get key statistics for dashboard widgets
        $stats = [
            'totalProducts' => Product::find()->count(),
            'totalOrders' => Order::find()->count(),
            'totalUsers' => User::find()->count(),
            'totalBlogPosts' => BlogPost::find()->count(),
            'pendingOrders' => Order::find()->where(['status' => 'pending'])->count(),
            'recentOrders' => Order::find()->orderBy(['created_at' => SORT_DESC])->limit(5)->all(),
            'lowStockProducts' => Product::find()->where(['<=', 'stock', 10])->limit(5)->all(),
        ];

        return $this->render('index', [
            'stats' => $stats,
        ]);
    }

    /**
     * Get sales data for charts (AJAX endpoint)
     * @return \yii\web\Response
     */
    public function actionSalesData()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        // Get sales data for the last 30 days
        $salesData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $sales = Order::find()
                ->where(['DATE(created_at)' => $date])
                ->andWhere(['status' => 'completed'])
                ->sum('total') ?: 0;
            
            $salesData[] = [
                'date' => $date,
                'sales' => (float)$sales
            ];
        }
        
        return $salesData;
    }
}
