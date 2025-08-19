<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ProductController implements product management functionality.
 */
class ProductController extends Controller
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
     * Lists all Product models.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find()->with('category'),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Product created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => Category::find()->all(),
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Product updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => Category::find()->all(),
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Product deleted successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Bulk actions for products
     * @return Response
     */
    public function actionBulkAction()
    {
        $action = Yii::$app->request->post('action');
        $ids = Yii::$app->request->post('selection', []);

        if (empty($ids)) {
            Yii::$app->session->setFlash('warning', 'Please select at least one product.');
            return $this->redirect(['index']);
        }

        switch ($action) {
            case 'activate':
                Product::updateAll(['status' => Product::STATUS_ACTIVE], ['id' => $ids]);
                Yii::$app->session->setFlash('success', 'Selected products activated.');
                break;
            case 'deactivate':
                Product::updateAll(['status' => Product::STATUS_INACTIVE], ['id' => $ids]);
                Yii::$app->session->setFlash('success', 'Selected products deactivated.');
                break;
            case 'delete':
                Product::deleteAll(['id' => $ids]);
                Yii::$app->session->setFlash('success', 'Selected products deleted.');
                break;
        }

        return $this->redirect(['index']);
    }

    /**
     * Upload product images via AJAX
     * @param int $id Product ID
     * @return Response
     */
    public function actionUploadImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = $this->findModel($id);
        $uploadedFile = UploadedFile::getInstanceByName('file');
        
        if ($uploadedFile) {
            // Here you would integrate with AWS S3 or local file storage
            // For now, return a success response
            return [
                'success' => true,
                'message' => 'Image uploaded successfully',
                'filename' => $uploadedFile->name
            ];
        }
        
        return [
            'success' => false,
            'message' => 'No file uploaded'
        ];
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
