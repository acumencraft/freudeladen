<?php

namespace backend\controllers;

use Yii;
use common\models\Transaction;
use common\models\PaymentMethod;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Transaction::find()->with(['paymentMethod']);
        
        // Search functionality
        if (Yii::$app->request->get('transaction_id')) {
            $query->andWhere(['like', 'transaction_id', Yii::$app->request->get('transaction_id')]);
        }
        
        if (Yii::$app->request->get('order_id')) {
            $query->andWhere(['order_id' => Yii::$app->request->get('order_id')]);
        }
        
        if (Yii::$app->request->get('payment_method_id')) {
            $query->andWhere(['payment_method_id' => Yii::$app->request->get('payment_method_id')]);
        }
        
        if (Yii::$app->request->get('type')) {
            $query->andWhere(['type' => Yii::$app->request->get('type')]);
        }
        
        if (Yii::$app->request->get('status')) {
            $query->andWhere(['status' => Yii::$app->request->get('status')]);
        }
        
        if (Yii::$app->request->get('date_from')) {
            $query->andWhere(['>=', 'created_at', Yii::$app->request->get('date_from') . ' 00:00:00']);
        }
        
        if (Yii::$app->request->get('date_to')) {
            $query->andWhere(['<=', 'created_at', Yii::$app->request->get('date_to') . ' 23:59:59']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'paymentMethods' => PaymentMethod::find()->all(),
        ]);
    }

    /**
     * Displays a single Transaction model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Transaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transaction();
        $model->transaction_id = Transaction::generateTransactionId();

        if ($model->load(Yii::$app->request->post())) {
            
            // Handle metadata array
            $metadata = Yii::$app->request->post('metadata', []);
            if (is_array($metadata)) {
                $model->setMetadataArray($metadata);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Transaction created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'paymentMethods' => PaymentMethod::getActive()->all(),
        ]);
    }

    /**
     * Updates an existing Transaction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
            // Handle metadata array
            $metadata = Yii::$app->request->post('metadata', []);
            if (is_array($metadata)) {
                $model->setMetadataArray($metadata);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Transaction updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'paymentMethods' => PaymentMethod::getActive()->all(),
        ]);
    }

    /**
     * Deletes an existing Transaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Only allow deletion of failed or cancelled transactions
        if (!$model->hasFailed()) {
            Yii::$app->session->setFlash('error', 'Only failed or cancelled transactions can be deleted.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Transaction deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Process refund for a transaction
     * @param integer $id
     * @return mixed
     */
    public function actionRefund($id)
    {
        $model = $this->findModel($id);
        $refundAmount = null;
        $refundReason = '';
        $refund = null;
        $error = null;
        
        if (!$model->canBeRefunded()) {
            Yii::$app->session->setFlash('error', 'This transaction cannot be refunded.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        if (Yii::$app->request->isPost) {
            $refundAmount = (float) Yii::$app->request->post('refund_amount');
            $refundReason = Yii::$app->request->post('refund_reason', '');
            
            if ($refundAmount <= 0 || $refundAmount > $model->amount) {
                $error = 'Invalid refund amount. Must be between 0 and ' . $model->amount;
            } else {
                $refund = $model->createRefund($refundAmount, $refundReason);
                if ($refund) {
                    Yii::$app->session->setFlash('success', 'Refund created successfully.');
                    return $this->redirect(['view', 'id' => $refund->id]);
                } else {
                    $error = 'Failed to create refund transaction.';
                }
            }
        }
        
        return $this->render('refund', [
            'model' => $model,
            'refundAmount' => $refundAmount,
            'refundReason' => $refundReason,
            'error' => $error,
        ]);
    }

    /**
     * Mark transaction as completed
     * @param integer $id
     * @return mixed
     */
    public function actionMarkCompleted($id)
    {
        $model = $this->findModel($id);
        
        if (!$model->isPending()) {
            Yii::$app->session->setFlash('error', 'Only pending transactions can be marked as completed.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        if ($model->markAsCompleted()) {
            Yii::$app->session->setFlash('success', 'Transaction marked as completed successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to update transaction status.');
        }
        
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Mark transaction as failed
     * @param integer $id
     * @return mixed
     */
    public function actionMarkFailed($id)
    {
        $model = $this->findModel($id);
        $reason = '';
        
        if (Yii::$app->request->isPost) {
            $reason = Yii::$app->request->post('failure_reason', 'Manually marked as failed');
            
            if ($model->markAsFailed($reason)) {
                Yii::$app->session->setFlash('success', 'Transaction marked as failed.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update transaction status.');
            }
        }
        
        return $this->render('mark-failed', [
            'model' => $model,
            'reason' => $reason,
        ]);
    }

    /**
     * Transaction analytics dashboard
     * @return mixed
     */
    public function actionAnalytics()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));
        
        // Transaction statistics
        $stats = [
            'total_transactions' => Transaction::findByDateRange($dateFrom, $dateTo)->count(),
            'completed_transactions' => Transaction::findByDateRange($dateFrom, $dateTo)->andWhere(['status' => Transaction::STATUS_COMPLETED])->count(),
            'failed_transactions' => Transaction::findByDateRange($dateFrom, $dateTo)->andWhere(['status' => Transaction::STATUS_FAILED])->count(),
            'total_amount' => Transaction::findByDateRange($dateFrom, $dateTo)->andWhere(['status' => Transaction::STATUS_COMPLETED])->sum('amount') ?: 0,
            'total_fees' => Transaction::findByDateRange($dateFrom, $dateTo)->andWhere(['status' => Transaction::STATUS_COMPLETED])->sum('fee') ?: 0,
        ];
        
        // Payment method statistics
        $methodStats = [];
        $methods = PaymentMethod::find()->all();
        foreach ($methods as $method) {
            $methodTransactions = Transaction::findByDateRange($dateFrom, $dateTo)
                ->andWhere(['payment_method_id' => $method->id, 'status' => Transaction::STATUS_COMPLETED]);
            
            $methodStats[] = [
                'method' => $method,
                'count' => $methodTransactions->count(),
                'amount' => $methodTransactions->sum('amount') ?: 0,
                'fees' => $methodTransactions->sum('fee') ?: 0,
            ];
        }
        
        // Daily transaction data for charts
        $dailyStats = [];
        $period = new \DatePeriod(
            new \DateTime($dateFrom),
            new \DateInterval('P1D'),
            new \DateTime($dateTo . ' +1 day')
        );
        
        foreach ($period as $date) {
            $dayStr = $date->format('Y-m-d');
            $dayTransactions = Transaction::findByDateRange($dayStr, $dayStr)
                ->andWhere(['status' => Transaction::STATUS_COMPLETED]);
            
            $dailyStats[] = [
                'date' => $dayStr,
                'count' => $dayTransactions->count(),
                'amount' => $dayTransactions->sum('amount') ?: 0,
            ];
        }
        
        return $this->render('analytics', [
            'stats' => $stats,
            'methodStats' => $methodStats,
            'dailyStats' => $dailyStats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Export transactions to CSV
     * @return mixed
     */
    public function actionExport()
    {
        $query = Transaction::find()->with(['paymentMethod']);
        
        // Apply same filters as index
        if (Yii::$app->request->get('status')) {
            $query->andWhere(['status' => Yii::$app->request->get('status')]);
        }
        
        if (Yii::$app->request->get('date_from')) {
            $query->andWhere(['>=', 'created_at', Yii::$app->request->get('date_from') . ' 00:00:00']);
        }
        
        if (Yii::$app->request->get('date_to')) {
            $query->andWhere(['<=', 'created_at', Yii::$app->request->get('date_to') . ' 23:59:59']);
        }
        
        $transactions = $query->all();
        
        $filename = 'transactions_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Transaction ID',
            'Order ID',
            'Payment Method',
            'Type',
            'Status',
            'Amount',
            'Currency',
            'Fee',
            'Net Amount',
            'Provider Transaction ID',
            'Created At',
            'Processed At',
        ]);
        
        // CSV data
        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $transaction->transaction_id,
                $transaction->order_id,
                $transaction->paymentMethod ? $transaction->paymentMethod->name : '',
                $transaction->getTypeLabel(),
                $transaction->getStatusLabel(),
                $transaction->amount,
                $transaction->currency,
                $transaction->fee,
                $transaction->getNetAmount(),
                $transaction->provider_transaction_id,
                $transaction->created_at,
                $transaction->processed_at,
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested transaction does not exist.');
    }
}
