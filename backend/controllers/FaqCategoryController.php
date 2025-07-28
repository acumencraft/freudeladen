<?php

namespace backend\controllers;

use Yii;
use common\models\FaqCategory;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FaqCategoryController implements the CRUD actions for FaqCategory model.
 */
class FaqCategoryController extends Controller
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
     * Lists all FaqCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = FaqCategory::find();
        
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
     * Displays a single FaqCategory model.
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
     * Creates a new FaqCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FaqCategory();

        if ($model->load(Yii::$app->request->post())) {
            
            // Generate slug if not provided
            if (empty($model->slug)) {
                $model->slug = FaqCategory::generateSlug($model->name);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'FAQ category created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FaqCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'FAQ category updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FaqCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if category has FAQs
        if ($model->getFaqCount() > 0) {
            Yii::$app->session->setFlash('error', 'Cannot delete category that contains FAQs. Please move or delete the FAQs first.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'FAQ category deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Toggle status of a FAQ category
     * @param integer $id
     * @return mixed
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == FaqCategory::STATUS_ACTIVE ? FaqCategory::STATUS_INACTIVE : FaqCategory::STATUS_ACTIVE;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'FAQ category status updated successfully.');
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
            $category = FaqCategory::findOne($id);
            if ($category) {
                $category->sort_order = $order + 1;
                $category->save(false);
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
            $categories = FaqCategory::find()->where(['id' => $ids])->all();
            $deletedCount = 0;
            $errorCount = 0;
            
            foreach ($categories as $category) {
                if ($category->getFaqCount() > 0) {
                    $errorCount++;
                    continue;
                }
                $category->delete();
                $deletedCount++;
            }
            
            if ($deletedCount > 0) {
                Yii::$app->session->setFlash('success', $deletedCount . ' categories deleted successfully.');
            }
            
            if ($errorCount > 0) {
                Yii::$app->session->setFlash('warning', $errorCount . ' categories could not be deleted because they contain FAQs.');
            }
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the FaqCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FaqCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FaqCategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested FAQ category does not exist.');
    }
}
