<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\ShippingZone;
use app\models\ShippingMethod;
use app\models\ShippingRate;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * ShippingController manages shipping zones, methods, and rates.
 */
class ShippingController extends Controller
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
                    'delete-zone' => ['POST'],
                    'delete-method' => ['POST'],
                    'delete-rate' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Shipping management overview.
     * @return string
     */
    public function actionIndex()
    {
        $zonesProvider = new ActiveDataProvider([
            'query' => ShippingZone::find()->with(['methods']),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC]
            ],
        ]);

        return $this->render('index', [
            'zonesProvider' => $zonesProvider,
        ]);
    }

    /**
     * Manage shipping zones.
     * @return string
     */
    public function actionZones()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ShippingZone::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC]
            ],
        ]);

        return $this->render('zones', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create a new shipping zone.
     * @return string|Response
     */
    public function actionCreateZone()
    {
        $model = new ShippingZone();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Shipping zone created successfully.');
                return $this->redirect(['view-zone', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
            $model->status = ShippingZone::STATUS_ACTIVE;
        }

        return $this->render('create-zone', [
            'model' => $model,
        ]);
    }

    /**
     * Update an existing shipping zone.
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateZone($id)
    {
        $model = $this->findZoneModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Shipping zone updated successfully.');
            return $this->redirect(['view-zone', 'id' => $model->id]);
        }

        return $this->render('update-zone', [
            'model' => $model,
        ]);
    }

    /**
     * View a shipping zone with its methods and rates.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewZone($id)
    {
        $model = $this->findZoneModel($id);
        
        $methodsProvider = new ActiveDataProvider([
            'query' => ShippingMethod::find()->where(['zone_id' => $id])->with(['rates']),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC]
            ],
        ]);

        return $this->render('view-zone', [
            'model' => $model,
            'methodsProvider' => $methodsProvider,
        ]);
    }

    /**
     * Delete a shipping zone.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteZone($id)
    {
        $model = $this->findZoneModel($id);
        
        // Check if zone has methods
        if (ShippingMethod::find()->where(['zone_id' => $id])->exists()) {
            Yii::$app->session->setFlash('error', 'Cannot delete zone with existing shipping methods.');
            return $this->redirect(['view-zone', 'id' => $id]);
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Shipping zone deleted successfully.');
        
        return $this->redirect(['zones']);
    }

    /**
     * Create a new shipping method.
     * @param int $zone_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCreateMethod($zone_id)
    {
        $zone = $this->findZoneModel($zone_id);
        $model = new ShippingMethod();
        $model->zone_id = $zone_id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Shipping method created successfully.');
                return $this->redirect(['view-method', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
            $model->status = ShippingMethod::STATUS_ACTIVE;
        }

        return $this->render('create-method', [
            'model' => $model,
            'zone' => $zone,
        ]);
    }

    /**
     * Update an existing shipping method.
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateMethod($id)
    {
        $model = $this->findMethodModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Shipping method updated successfully.');
            return $this->redirect(['view-method', 'id' => $model->id]);
        }

        return $this->render('update-method', [
            'model' => $model,
        ]);
    }

    /**
     * View a shipping method with its rates.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewMethod($id)
    {
        $model = $this->findMethodModel($id);
        
        $ratesProvider = new ActiveDataProvider([
            'query' => ShippingRate::find()->where(['method_id' => $id]),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['min_weight' => SORT_ASC]
            ],
        ]);

        return $this->render('view-method', [
            'model' => $model,
            'ratesProvider' => $ratesProvider,
        ]);
    }

    /**
     * Delete a shipping method.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteMethod($id)
    {
        $model = $this->findMethodModel($id);
        $zone_id = $model->zone_id;
        
        // Delete all associated rates
        ShippingRate::deleteAll(['method_id' => $id]);
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Shipping method deleted successfully.');
        
        return $this->redirect(['view-zone', 'id' => $zone_id]);
    }

    /**
     * Create a new shipping rate.
     * @param int $method_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCreateRate($method_id)
    {
        $method = $this->findMethodModel($method_id);
        $model = new ShippingRate();
        $model->method_id = $method_id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Shipping rate created successfully.');
                return $this->redirect(['view-method', 'id' => $method_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create-rate', [
            'model' => $model,
            'method' => $method,
        ]);
    }

    /**
     * Update an existing shipping rate.
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateRate($id)
    {
        $model = $this->findRateModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Shipping rate updated successfully.');
            return $this->redirect(['view-method', 'id' => $model->method_id]);
        }

        return $this->render('update-rate', [
            'model' => $model,
        ]);
    }

    /**
     * Delete a shipping rate.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteRate($id)
    {
        $model = $this->findRateModel($id);
        $method_id = $model->method_id;
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Shipping rate deleted successfully.');
        
        return $this->redirect(['view-method', 'id' => $method_id]);
    }

    /**
     * Calculate shipping cost for testing.
     * @return Response
     */
    public function actionCalculateShipping()
    {
        $weight = (float) Yii::$app->request->post('weight', 0);
        $country = Yii::$app->request->post('country', '');
        $state = Yii::$app->request->post('state', '');
        $city = Yii::$app->request->post('city', '');

        $results = [];

        // Find applicable shipping zones
        $zones = ShippingZone::find()
            ->where(['status' => ShippingZone::STATUS_ACTIVE])
            ->all();

        foreach ($zones as $zone) {
            // Check if location matches zone (simplified logic)
            if ($this->locationMatchesZone($country, $state, $city, $zone)) {
                $methods = ShippingMethod::find()
                    ->where(['zone_id' => $zone->id, 'status' => ShippingMethod::STATUS_ACTIVE])
                    ->all();

                foreach ($methods as $method) {
                    $rate = ShippingRate::find()
                        ->where(['method_id' => $method->id])
                        ->andWhere(['<=', 'min_weight', $weight])
                        ->andWhere(['or', ['max_weight' => null], ['>=', 'max_weight', $weight]])
                        ->one();

                    if ($rate) {
                        $results[] = [
                            'zone' => $zone->name,
                            'method' => $method->name,
                            'cost' => $rate->cost,
                            'estimated_days' => $method->estimated_days,
                        ];
                    }
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $results;
    }

    /**
     * Find the ShippingZone model based on its primary key value.
     * @param int $id
     * @return ShippingZone
     * @throws NotFoundHttpException
     */
    protected function findZoneModel($id)
    {
        if (($model = ShippingZone::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested shipping zone does not exist.');
    }

    /**
     * Find the ShippingMethod model based on its primary key value.
     * @param int $id
     * @return ShippingMethod
     * @throws NotFoundHttpException
     */
    protected function findMethodModel($id)
    {
        if (($model = ShippingMethod::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested shipping method does not exist.');
    }

    /**
     * Find the ShippingRate model based on its primary key value.
     * @param int $id
     * @return ShippingRate
     * @throws NotFoundHttpException
     */
    protected function findRateModel($id)
    {
        if (($model = ShippingRate::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested shipping rate does not exist.');
    }

    /**
     * Check if a location matches a shipping zone.
     * @param string $country
     * @param string $state
     * @param string $city
     * @param ShippingZone $zone
     * @return bool
     */
    private function locationMatchesZone($country, $state, $city, $zone)
    {
        // Simplified logic - in a real application, you would have
        // more sophisticated zone matching based on countries, states, zip codes, etc.
        return true; // For now, match all zones
    }
}
