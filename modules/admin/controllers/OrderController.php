<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Order;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * OrderController implements order management functionality.
 */
class OrderController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->with('user'),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
        ]);

        // Filter by status if provided
        $status = Yii::$app->request->get('status');
        if ($status) {
            $dataProvider->query->andWhere(['status' => $status]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Updates order status.
     * @param int $id Order ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateStatus($id)
    {
        $model = $this->findModel($id);
        $newStatus = Yii::$app->request->post('status');
        
        if (in_array($newStatus, [
            Order::STATUS_PENDING, 
            Order::STATUS_PROCESSING, 
            Order::STATUS_SHIPPED, 
            Order::STATUS_DELIVERED, 
            Order::STATUS_CANCELLED,
            Order::STATUS_COMPLETED
        ])) {
            $model->status = $newStatus;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Order status updated successfully.');
                
                // Send email notification to customer
                $this->sendStatusUpdateEmail($model);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update order status.');
            }
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Updates payment status.
     * @param int $id Order ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdatePaymentStatus($id)
    {
        $model = $this->findModel($id);
        $newStatus = Yii::$app->request->post('payment_status');
        
        if (in_array($newStatus, [
            Order::PAYMENT_PENDING, 
            Order::PAYMENT_PAID, 
            Order::PAYMENT_FAILED, 
            Order::PAYMENT_REFUNDED
        ])) {
            $model->payment_status = $newStatus;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Payment status updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update payment status.');
            }
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Export orders to CSV
     * @return Response
     */
    public function actionExport()
    {
        $query = Order::find()->with(['user', 'orderItems']);
        
        // Apply filters
        $status = Yii::$app->request->get('status');
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        
        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');
        
        if ($dateFrom) {
            $query->andWhere(['>=', 'created_at', $dateFrom]);
        }
        
        if ($dateTo) {
            $query->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59']);
        }
        
        $orders = $query->all();
        
        // Generate CSV
        $filename = 'orders_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/csv');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Order ID', 'Customer Email', 'Status', 'Payment Status', 
            'Total', 'Payment Method', 'Created At'
        ]);
        
        // CSV data
        foreach ($orders as $order) {
            fputcsv($output, [
                $order->id,
                $order->user ? $order->user->email : 'Guest',
                $order->status,
                $order->payment_status,
                $order->total,
                $order->payment_method,
                $order->created_at
            ]);
        }
        
        fclose($output);
        return Yii::$app->response;
    }

    /**
     * Get order statistics for dashboard
     * @return Response
     */
    public function actionStatistics()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $stats = [
            'total_orders' => Order::find()->count(),
            'pending_orders' => Order::find()->where(['status' => Order::STATUS_PENDING])->count(),
            'completed_orders' => Order::find()->where(['status' => Order::STATUS_COMPLETED])->count(),
            'total_revenue' => Order::find()->where(['payment_status' => Order::PAYMENT_PAID])->sum('total'),
            'todays_orders' => Order::find()->where(['>=', 'created_at', date('Y-m-d 00:00:00')])->count(),
            'todays_revenue' => Order::find()
                ->where(['>=', 'created_at', date('Y-m-d 00:00:00')])
                ->andWhere(['payment_status' => Order::PAYMENT_PAID])
                ->sum('total'),
        ];
        
        return $stats;
    }

    /**
     * Send status update email to customer
     * @param Order $order
     */
    private function sendStatusUpdateEmail($order)
    {
        if ($order->user && $order->user->email) {
            // Here you would implement email sending
            // For now, just log the action
            Yii::info("Order status update email sent to {$order->user->email} for order #{$order->id}", 'order');
        }
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
