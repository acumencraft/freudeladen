<?php

namespace backend\controllers;

use Yii;
use common\models\ShippingMethod;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShippingMethodController implements the CRUD actions for ShippingMethod model.
 */
class ShippingMethodController extends Controller
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
     * Lists all ShippingMethod models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = ShippingMethod::find();
        
        // Search functionality
        if (Yii::$app->request->get('name')) {
            $query->andWhere(['like', 'name', Yii::$app->request->get('name')]);
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
     * Displays a single ShippingMethod model.
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
     * Creates a new ShippingMethod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ShippingMethod();

        if ($model->load(Yii::$app->request->post())) {
            
            // Handle settings array
            $settings = Yii::$app->request->post('settings', []);
            if (is_array($settings)) {
                $model->settings = json_encode($settings);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Shipping method created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShippingMethod model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
            // Handle settings array
            $settings = Yii::$app->request->post('settings', []);
            if (is_array($settings)) {
                $model->settings = json_encode($settings);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Shipping method updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShippingMethod model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if method has shipping rates
        if ($model->getRatesCount() > 0) {
            Yii::$app->session->setFlash('error', 'Cannot delete shipping method that has shipping rates. Please delete the rates first.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Shipping method deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Toggle status of a shipping method
     * @param integer $id
     * @return mixed
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == ShippingMethod::STATUS_ACTIVE ? ShippingMethod::STATUS_INACTIVE : ShippingMethod::STATUS_ACTIVE;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Shipping method status updated successfully.');
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
            $method = ShippingMethod::findOne($id);
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
            $methods = ShippingMethod::find()->where(['id' => $ids])->all();
            $deletedCount = 0;
            $errorCount = 0;
            
            foreach ($methods as $method) {
                if ($method->getRatesCount() > 0) {
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
                Yii::$app->session->setFlash('warning', $errorCount . ' methods could not be deleted because they have shipping rates.');
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
            ShippingMethod::updateAll(['status' => $status], ['id' => $ids]);
            $statusLabel = $status == ShippingMethod::STATUS_ACTIVE ? 'activated' : 'deactivated';
            Yii::$app->session->setFlash('success', count($ids) . ' methods ' . $statusLabel . ' successfully.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the ShippingMethod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShippingMethod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShippingMethod::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested shipping method does not exist.');
    }
}
