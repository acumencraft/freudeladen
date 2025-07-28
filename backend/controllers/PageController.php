<?php

namespace backend\controllers;

use Yii;
use common\models\Page;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * PageController implements static page management functionality.
 */
class PageController extends Controller
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
     * Lists all Page models.
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new \yii\base\DynamicModel(['title', 'status']);
        $searchModel->addRule(['title'], 'string');
        $searchModel->addRule(['status'], 'integer');

        $query = Page::find();

        if ($searchModel->load(Yii::$app->request->queryParams) && $searchModel->validate()) {
            if (!empty($searchModel->title)) {
                $query->andFilterWhere(['like', 'title', $searchModel->title]);
            }
            if ($searchModel->status !== null) {
                $query->andFilterWhere(['status' => $searchModel->status]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Page model.
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
     * Creates a new Page model.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Page();
        $model->status = Page::STATUS_DRAFT;

        if ($model->load(Yii::$app->request->post())) {
            // Auto-generate slug if not provided
            if (empty($model->slug)) {
                $model->slug = Page::generateSlug($model->title);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Page created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Page model.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Page updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Page model.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Page deleted successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Toggle page status.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == Page::STATUS_PUBLISHED ? Page::STATUS_DRAFT : Page::STATUS_PUBLISHED;
        
        if ($model->save()) {
            $status = $model->status == Page::STATUS_PUBLISHED ? 'published' : 'drafted';
            Yii::$app->session->setFlash('success', "Page has been {$status}.");
        } else {
            Yii::$app->session->setFlash('error', 'Failed to update page status.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Preview page content.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPreview($id)
    {
        $model = $this->findModel($id);
        
        return $this->renderPartial('preview', [
            'model' => $model,
        ]);
    }

    /**
     * AJAX action to generate slug from title.
     * @return Response
     */
    public function actionGenerateSlug()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $title = Yii::$app->request->post('title', '');
        $slug = Page::generateSlug($title);
        
        return ['slug' => $slug];
    }

    /**
     * Finds the Page model based on its primary key value.
     * @param int $id ID
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Page::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
