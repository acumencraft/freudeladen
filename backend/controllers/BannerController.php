<?php

namespace backend\controllers;

use Yii;
use common\models\Banner;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\web\Response;

/**
 * BannerController implements banner management functionality.
 */
class BannerController extends Controller
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
                    'toggle-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Banner models.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Banner::find()->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Banner model.
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
     * Creates a new Banner model.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Banner();

        if ($model->load(Yii::$app->request->post())) {
            // Handle image upload
            $imageFile = UploadedFile::getInstance($model, 'image_path');
            if ($imageFile) {
                $uploadPath = Yii::getAlias('@webroot/uploads/banners/');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $fileName = time() . '_' . $imageFile->baseName . '.' . $imageFile->extension;
                $filePath = $uploadPath . $fileName;
                
                if ($imageFile->saveAs($filePath)) {
                    $model->image_path = $fileName;
                }
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Banner created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Banner model.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImagePath = $model->image_path;

        if ($model->load(Yii::$app->request->post())) {
            // Handle image upload
            $imageFile = UploadedFile::getInstance($model, 'image_path');
            if ($imageFile) {
                $uploadPath = Yii::getAlias('@webroot/uploads/banners/');
                $fileName = time() . '_' . $imageFile->baseName . '.' . $imageFile->extension;
                $filePath = $uploadPath . $fileName;
                
                if ($imageFile->saveAs($filePath)) {
                    // Delete old image
                    if ($oldImagePath && file_exists($uploadPath . $oldImagePath)) {
                        unlink($uploadPath . $oldImagePath);
                    }
                    $model->image_path = $fileName;
                }
            } else {
                $model->image_path = $oldImagePath;
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Banner updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Banner model.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Delete associated image file
        if ($model->image_path) {
            $imagePath = Yii::getAlias('@webroot/uploads/banners/') . $model->image_path;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $model->delete();
        Yii::$app->session->setFlash('success', 'Banner deleted successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Toggle banner status.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->is_active = $model->is_active ? 0 : 1;
        
        if ($model->save()) {
            $status = $model->is_active ? 'activated' : 'deactivated';
            Yii::$app->session->setFlash('success', "Banner has been {$status}.");
        } else {
            Yii::$app->session->setFlash('error', 'Failed to update banner status.');
        }

        return $this->redirect(['index']);
    }

    /**
     * AJAX action to update banner sort order.
     * @return Response
     */
    public function actionUpdateSort()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $ids = Yii::$app->request->post('ids', []);
        
        foreach ($ids as $index => $id) {
            Banner::updateAll(['sort_order' => $index + 1], ['id' => $id]);
        }
        
        return ['success' => true];
    }

    /**
     * Finds the Banner model based on its primary key value.
     * @param int $id ID
     * @return Banner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Banner::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
