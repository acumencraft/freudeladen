<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * CategoryController implements category management functionality.
 */
class CategoryController extends Controller
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
     * Lists all Category models in a tree structure.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Category::find()->orderBy(['parent_id' => SORT_ASC, 'name' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Category created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'parentCategories' => Category::find()->where(['parent_id' => null])->all(),
        ]);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Category updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'parentCategories' => Category::find()->where(['!=', 'id', $id])->andWhere(['parent_id' => null])->all(),
        ]);
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if category has products
        if ($model->getProducts()->count() > 0) {
            Yii::$app->session->setFlash('error', 'Cannot delete category with existing products.');
            return $this->redirect(['index']);
        }
        
        // Check if category has subcategories
        if ($model->getChildren()->count() > 0) {
            Yii::$app->session->setFlash('error', 'Cannot delete category with subcategories.');
            return $this->redirect(['index']);
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'Category deleted successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Reorder categories via drag and drop (AJAX)
     * @return Response
     */
    public function actionReorder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $data = Yii::$app->request->post('data', []);
        
        foreach ($data as $item) {
            $category = Category::findOne($item['id']);
            if ($category) {
                $category->parent_id = isset($item['parent_id']) ? $item['parent_id'] : null;
                $category->sort_order = isset($item['sort_order']) ? $item['sort_order'] : 0;
                $category->save(false);
            }
        }
        
        return ['success' => true];
    }

    /**
     * Get category tree for AJAX requests
     * @return Response
     */
    public function actionTree()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $categories = Category::find()->orderBy(['parent_id' => SORT_ASC, 'sort_order' => SORT_ASC])->all();
        
        return $this->buildTree($categories);
    }

    /**
     * Build hierarchical tree structure
     * @param array $categories
     * @param int|null $parentId
     * @return array
     */
    private function buildTree($categories, $parentId = null)
    {
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $node = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'status' => $category->status,
                    'children' => $this->buildTree($categories, $category->id)
                ];
                $tree[] = $node;
            }
        }
        
        return $tree;
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
