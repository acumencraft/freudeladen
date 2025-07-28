<?php

namespace backend\controllers;

use Yii;
use common\models\BlogCategory;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BlogCategoryController implements the CRUD actions for BlogCategory model.
 */
class BlogCategoryController extends Controller
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
     * Lists all BlogCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = BlogCategory::find();
        
        // Search functionality
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
                    'name' => SORT_ASC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BlogCategory model.
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
     * Creates a new BlogCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BlogCategory();

        if ($model->load(Yii::$app->request->post())) {
            
            // Generate slug if not provided
            if (empty($model->slug)) {
                $model->slug = BlogCategory::generateSlug($model->name);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Blog category created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BlogCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Blog category updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BlogCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if category has posts
        if ($model->getPostCount() > 0) {
            Yii::$app->session->setFlash('error', 'Cannot delete category that contains blog posts. Please move or delete the posts first.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Blog category deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Bulk delete action
     * @return mixed
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->post('selection', []);
        if (!empty($ids)) {
            $categories = BlogCategory::find()->where(['id' => $ids])->all();
            $deletedCount = 0;
            $errorCount = 0;
            
            foreach ($categories as $category) {
                if ($category->getPostCount() > 0) {
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
                Yii::$app->session->setFlash('warning', $errorCount . ' categories could not be deleted because they contain blog posts.');
            }
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the BlogCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BlogCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogCategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested blog category does not exist.');
    }
}
