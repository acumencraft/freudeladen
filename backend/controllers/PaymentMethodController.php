<?php

namespace backend\controllers;

use Yii;
use common\models\PaymentMethod;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaymentMethodController implements the CRUD actions for PaymentMethod model.
 */
class PaymentMethodController extends Controller
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
     * Lists all PaymentMethod models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = PaymentMethod::find();
        
        // Search functionality
        if (Yii::$app->request->get('name')) {
            $query->andWhere(['like', 'name', Yii::$app->request->get('name')]);
        }
        
        if (Yii::$app->request->get('type')) {
            $query->andWhere(['type' => Yii::$app->request->get('type')]);
        }
        
        if (Yii::$app->request->get('provider')) {
            $query->andWhere(['provider' => Yii::$app->request->get('provider')]);
        }
        
        if (Yii::$app->request->get('status') !== null && Yii::$app->request->get('status') !== '') {
            $query->andWhere(['status' => Yii::$app->request->get('status')]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_ASC,
                    'name' => SORT_ASC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PaymentMethod model.
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
     * Creates a new PaymentMethod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PaymentMethod();

        if ($model->load(Yii::$app->request->post())) {
            
            // Handle config array
            $configKeys = Yii::$app->request->post('config_keys', []);
            $configValues = Yii::$app->request->post('config_values', []);
            $config = [];
            
            foreach ($configKeys as $index => $key) {
                if (!empty($key) && isset($configValues[$index])) {
                    $config[$key] = $configValues[$index];
                }
            }
            
            if (!empty($config)) {
                $model->setConfigArray($config);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Payment method created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PaymentMethod model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
            // Handle config array
            $configKeys = Yii::$app->request->post('config_keys', []);
            $configValues = Yii::$app->request->post('config_values', []);
            $config = [];
            
            foreach ($configKeys as $index => $key) {
                if (!empty($key) && isset($configValues[$index])) {
                    $config[$key] = $configValues[$index];
                }
            }
            
            if (!empty($config)) {
                $model->setConfigArray($config);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Payment method updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PaymentMethod model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if method has transactions
        if ($model->getTransactionCount() > 0) {
            Yii::$app->session->setFlash('error', 'Cannot delete payment method that has transactions. Please deactivate it instead.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Payment method deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Toggle status of a payment method
     * @param integer $id
     * @return mixed
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->is_active = $model->is_active ? 0 : 1;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Payment method status updated successfully.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Update sort order via AJAX
     * @return array
     */
    public function actionUpdateSort()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $ids = Yii::$app->request->post('ids', []);
        
        foreach ($ids as $order => $id) {
            $method = PaymentMethod::findOne($id);
            if ($method) {
                $method->sort_order = $order + 1;
                $method->save(false);
            }
        }
        
        return ['success' => true];
    }

    /**
     * Bulk delete action
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->post('selection', []);
        if (!empty($ids)) {
            $methods = PaymentMethod::find()->where(['id' => $ids])->all();
            $deletedCount = 0;
            $errorCount = 0;
            
            foreach ($methods as $method) {
                if ($method->getTransactionCount() > 0) {
                    $errorCount++;
                    continue;
                }
                $method->delete();
                $deletedCount++;
            }
            
            if ($deletedCount > 0) {
                Yii::$app->session->setFlash('success', $deletedCount . ' methods deleted successfully.');
            }
            
            if ($errorCount > 0) {
                Yii::$app->session->setFlash('warning', $errorCount . ' methods could not be deleted because they have transactions.');
            }
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Bulk status update action
     * @return mixed
     */
    public function actionBulkStatus()
    {
        $ids = Yii::$app->request->post('selection', []);
        $status = Yii::$app->request->post('bulk_status');
        
        if (!empty($ids) && $status !== null) {
            PaymentMethod::updateAll(['status' => $status], ['id' => $ids]);
            $statusLabel = $status == PaymentMethod::STATUS_ACTIVE ? 'activated' : 'deactivated';
            Yii::$app->session->setFlash('success', count($ids) . ' methods ' . $statusLabel . ' successfully.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Test payment method configuration
     * @param integer $id
     * @return mixed
     */
    public function actionTest($id)
    {
        $model = $this->findModel($id);
        $testResult = null;
        $error = null;
        
        if (Yii::$app->request->isPost) {
            $testAmount = Yii::$app->request->post('test_amount', 10.00);
            
            try {
                // Here you would implement actual payment provider testing
                // For now, we'll simulate a test
                if ($model->status == PaymentMethod::STATUS_ACTIVE) {
                    $fee = $model->calculateFee($testAmount);
                    $testResult = [
                        'success' => true,
                        'amount' => $testAmount,
                        'fee' => $fee,
                        'total' => $testAmount + $fee,
                        'message' => 'Payment method configuration is valid and working.',
                    ];
                } else {
                    $error = 'Payment method is inactive and cannot process payments.';
                }
            } catch (\Exception $e) {
                $error = 'Test failed: ' . $e->getMessage();
            }
        }
        
        return $this->render('test', [
            'model' => $model,
            'testResult' => $testResult,
            'error' => $error,
        ]);
    }

    /**
     * Payment statistics
     * @return mixed
     */
    public function actionStats()
    {
        $stats = [];
        $methods = PaymentMethod::find()->all();
        
        foreach ($methods as $method) {
            $stats[] = [
                'method' => $method,
                'transaction_count' => $method->getTransactionCount(),
                'total_amount' => $method->getTotalAmount(),
            ];
        }
        
        // Sort by total amount descending
        usort($stats, function($a, $b) {
            return $b['total_amount'] <=> $a['total_amount'];
        });
        
        return $this->render('stats', [
            'stats' => $stats,
        ]);
    }

    /**
     * Finds the PaymentMethod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentMethod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentMethod::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested payment method does not exist.');
    }
}
