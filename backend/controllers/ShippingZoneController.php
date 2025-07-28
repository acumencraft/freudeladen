<?php

namespace backend\controllers;

use Yii;
use common\models\ShippingZone;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShippingZoneController implements the CRUD actions for ShippingZone model.
 */
class ShippingZoneController extends Controller
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
     * Lists all ShippingZone models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = ShippingZone::find();
        
        // Search functionality
        if (Yii::$app->request->get('name')) {
            $query->andWhere(['like', 'name', Yii::$app->request->get('name')]);
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
     * Displays a single ShippingZone model.
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
     * Creates a new ShippingZone model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ShippingZone();

        if ($model->load(Yii::$app->request->post())) {
            
            // Handle countries array
            $countries = Yii::$app->request->post('countries', []);
            if (is_array($countries)) {
                $model->countries = implode(',', $countries);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Shipping zone created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ShippingZone model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
            // Handle countries array
            $countries = Yii::$app->request->post('countries', []);
            if (is_array($countries)) {
                $model->countries = implode(',', $countries);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Shipping zone updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ShippingZone model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if zone has shipping rates
        if ($model->getRatesCount() > 0) {
            Yii::$app->session->setFlash('error', 'Cannot delete shipping zone that has shipping rates. Please delete the rates first.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Shipping zone deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Toggle status of a shipping zone
     * @param integer $id
     * @return mixed
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == ShippingZone::STATUS_ACTIVE ? ShippingZone::STATUS_INACTIVE : ShippingZone::STATUS_ACTIVE;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Shipping zone status updated successfully.');
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
            $zone = ShippingZone::findOne($id);
            if ($zone) {
                $zone->sort_order = $order + 1;
                $zone->save(false);
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
            $zones = ShippingZone::find()->where(['id' => $ids])->all();
            $deletedCount = 0;
            $errorCount = 0;
            
            foreach ($zones as $zone) {
                if ($zone->getRatesCount() > 0) {
                    $errorCount++;
                    continue;
                }
                $zone->delete();
                $deletedCount++;
            }
            
            if ($deletedCount > 0) {
                Yii::$app->session->setFlash('success', $deletedCount . ' zones deleted successfully.');
            }
            
            if ($errorCount > 0) {
                Yii::$app->session->setFlash('warning', $errorCount . ' zones could not be deleted because they have shipping rates.');
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
            ShippingZone::updateAll(['status' => $status], ['id' => $ids]);
            $statusLabel = $status == ShippingZone::STATUS_ACTIVE ? 'activated' : 'deactivated';
            Yii::$app->session->setFlash('success', count($ids) . ' zones ' . $statusLabel . ' successfully.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Get available countries list
     * @return array
     */
    public static function getCountriesList()
    {
        return [
            'DE' => 'Germany',
            'AT' => 'Austria',
            'CH' => 'Switzerland',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'PT' => 'Portugal',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'LU' => 'Luxembourg',
            'DK' => 'Denmark',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'FI' => 'Finland',
            'PL' => 'Poland',
            'CZ' => 'Czech Republic',
            'SK' => 'Slovakia',
            'HU' => 'Hungary',
            'SI' => 'Slovenia',
            'HR' => 'Croatia',
            'EE' => 'Estonia',
            'LV' => 'Latvia',
            'LT' => 'Lithuania',
        ];
    }

    /**
     * Finds the ShippingZone model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShippingZone the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShippingZone::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested shipping zone does not exist.');
    }
}
