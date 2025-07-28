<?php

namespace backend\controllers;

use Yii;
use common\models\ShippingRate;
use common\models\ShippingZone;
use common\models\ShippingMethod;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShippingRateController implements the CRUD actions for ShippingRate model.
 */
class ShippingRateController extends Controller
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
     * Lists all ShippingRate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = ShippingRate::find()->with(['shippingZone', 'shippingMethod']);
        
        // Search functionality
        if (Yii::$app->request->get('zone_id')) {
            $query->andWhere(['shipping_zone_id' => Yii::$app->request->get('zone_id')]);
        }
        
        if (Yii::$app->request->get('method_id')) {
            $query->andWhere(['shipping_method_id' => Yii::$app->request->get('method_id')]);
        }
        
        if (Yii::$app->request->get('status') !== null && Yii::$app->request->get('status') !== '') {
            $query->andWhere(['status' => Yii::$app->request->get('status')]);
        }
        
        if (Yii::$app->request->get('name')) {
            $query->andWhere(['like', 'name', Yii::$app->request->get('name')]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'shipping_zone_id' => SORT_ASC,
                    'shipping_method_id' => SORT_ASC,
                    'sort_order' => SORT_ASC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'zones' => ShippingZone::find()->where(['status' => ShippingZone::STATUS_ACTIVE])->all(),
            'methods' => ShippingMethod::find()->where(['status' => ShippingMethod::STATUS_ACTIVE])->all(),
        ]);
    }

    /**
     * Displays a single ShippingRate model.
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
     * Creates a new ShippingRate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ShippingRate();
        
        // Pre-fill method ID if provided
        if (Yii::$app->request->get('method_id')) {
            $model->shipping_method_id = Yii::$app->request->get('method_id');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Shipping rate created successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'zones' => ShippingZone::find()->where(['status' => ShippingZone::STATUS_ACTIVE])->all(),
            'methods' => ShippingMethod::find()->where(['status' => ShippingMethod::STATUS_ACTIVE])->all(),
        ]);
    }

    /**
     * Updates an existing ShippingRate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Shipping rate updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'zones' => ShippingZone::find()->where(['status' => ShippingZone::STATUS_ACTIVE])->all(),
            'methods' => ShippingMethod::find()->where(['status' => ShippingMethod::STATUS_ACTIVE])->all(),
        ]);
    }

    /**
     * Deletes an existing ShippingRate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        
        Yii::$app->session->setFlash('success', 'Shipping rate deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Toggle status of a shipping rate
     * @param integer $id
     * @return mixed
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == ShippingRate::STATUS_ACTIVE ? ShippingRate::STATUS_INACTIVE : ShippingRate::STATUS_ACTIVE;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Shipping rate status updated successfully.');
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
            $rate = ShippingRate::findOne($id);
            if ($rate) {
                $rate->sort_order = $order + 1;
                $rate->save(false);
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
            ShippingRate::deleteAll(['id' => $ids]);
            Yii::$app->session->setFlash('success', count($ids) . ' rates deleted successfully.');
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
            ShippingRate::updateAll(['status' => $status], ['id' => $ids]);
            $statusLabel = $status == ShippingRate::STATUS_ACTIVE ? 'activated' : 'deactivated';
            Yii::$app->session->setFlash('success', count($ids) . ' rates ' . $statusLabel . ' successfully.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Duplicate a shipping rate
     * @param integer $id
     * @return mixed
     */
    public function actionDuplicate($id)
    {
        $original = $this->findModel($id);
        
        $model = new ShippingRate();
        $model->attributes = $original->attributes;
        $model->id = null;
        $model->name = $original->name . ' (Copy)';
        $model->created_at = null;
        $model->updated_at = null;
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Shipping rate duplicated successfully.');
            return $this->redirect(['update', 'id' => $model->id]);
        }
        
        Yii::$app->session->setFlash('error', 'Failed to duplicate shipping rate.');
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Calculate shipping cost for testing
     * @return mixed
     */
    public function actionCalculate()
    {
        $result = null;
        $error = null;
        
        if (Yii::$app->request->isPost) {
            $zoneId = Yii::$app->request->post('zone_id');
            $methodId = Yii::$app->request->post('method_id');
            $weight = Yii::$app->request->post('weight');
            $orderValue = Yii::$app->request->post('order_value');
            
            if ($zoneId && $methodId && $weight && $orderValue) {
                $rates = ShippingRate::findApplicable($zoneId, $methodId, $weight, $orderValue);
                
                if ($rates) {
                    $result = [];
                    foreach ($rates as $rate) {
                        $cost = $rate->calculateCost($weight, $orderValue);
                        $result[] = [
                            'rate' => $rate,
                            'cost' => $cost,
                        ];
                    }
                } else {
                    $error = 'No applicable shipping rates found for the given parameters.';
                }
            } else {
                $error = 'Please fill in all required fields.';
            }
        }
        
        return $this->render('calculate', [
            'zones' => ShippingZone::find()->where(['status' => ShippingZone::STATUS_ACTIVE])->all(),
            'methods' => ShippingMethod::find()->where(['status' => ShippingMethod::STATUS_ACTIVE])->all(),
            'result' => $result,
            'error' => $error,
        ]);
    }

    /**
     * Import shipping rates from CSV
     * @return mixed
     */
    public function actionImport()
    {
        if (Yii::$app->request->isPost) {
            $uploadedFile = \yii\web\UploadedFile::getInstanceByName('csv_file');
            
            if ($uploadedFile && $uploadedFile->extension === 'csv') {
                $csvData = file_get_contents($uploadedFile->tempName);
                $lines = explode("\n", $csvData);
                
                $imported = 0;
                $errors = [];
                
                // Skip header line
                for ($i = 1; $i < count($lines); $i++) {
                    $line = trim($lines[$i]);
                    if (empty($line)) continue;
                    
                    $data = str_getcsv($line);
                    
                    if (count($data) >= 8) {
                        $rate = new ShippingRate();
                        $rate->name = $data[0];
                        $rate->shipping_zone_id = $data[1];
                        $rate->shipping_method_id = $data[2];
                        $rate->min_weight = $data[3];
                        $rate->max_weight = $data[4];
                        $rate->min_order_value = $data[5];
                        $rate->cost = $data[6];
                        $rate->free_shipping_threshold = $data[7];
                        $rate->status = ShippingRate::STATUS_ACTIVE;
                        
                        if ($rate->save()) {
                            $imported++;
                        } else {
                            $errors[] = "Line " . ($i + 1) . ": " . implode(', ', $rate->getFirstErrors());
                        }
                    }
                }
                
                if ($imported > 0) {
                    Yii::$app->session->setFlash('success', $imported . ' shipping rates imported successfully.');
                }
                
                if (!empty($errors)) {
                    Yii::$app->session->setFlash('warning', 'Some errors occurred: ' . implode('<br>', $errors));
                }
                
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Please upload a valid CSV file.');
            }
        }
        
        return $this->render('import');
    }

    /**
     * Finds the ShippingRate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShippingRate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShippingRate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested shipping rate does not exist.');
    }
}
