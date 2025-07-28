<?php

namespace backend\controllers;

use Yii;
use common\models\Faq;
use common\models\FaqCategory;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * FaqController implements the CRUD actions for Faq model.
 */
class FaqController extends Controller
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
     * Lists all Faq models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Faq::find()->with(['category']);
        
        // Search functionality
        if (Yii::$app->request->get('question')) {
            $query->andWhere(['like', 'question', Yii::$app->request->get('question')]);
        }
        
        if (Yii::$app->request->get('category_id')) {
            $query->andWhere(['category_id' => Yii::$app->request->get('category_id')]);
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
                    'id' => SORT_ASC,
                ]
            ],
        ]);

        $categories = ArrayHelper::map(FaqCategory::find()->all(), 'id', 'name');

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'categories' => $categories,
        ]);
    }

    /**
     * Displays a single Faq model.
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
     * Creates a new Faq model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Faq();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'FAQ created successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => ArrayHelper::map(FaqCategory::find()->all(), 'id', 'name'),
        ]);
    }

    /**
     * Updates an existing Faq model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'FAQ updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => ArrayHelper::map(FaqCategory::find()->all(), 'id', 'name'),
        ]);
    }

    /**
     * Deletes an existing Faq model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        
        Yii::$app->session->setFlash('success', 'FAQ deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Toggle status of a FAQ
     * @param integer $id
     * @return mixed
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == Faq::STATUS_ACTIVE ? Faq::STATUS_INACTIVE : Faq::STATUS_ACTIVE;
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'FAQ status updated successfully.');
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
            $faq = Faq::findOne($id);
            if ($faq) {
                $faq->sort_order = $order + 1;
                $faq->save(false);
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
            Faq::deleteAll(['id' => $ids]);
            Yii::$app->session->setFlash('success', count($ids) . ' FAQs deleted successfully.');
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
            Faq::updateAll(['status' => $status], ['id' => $ids]);
            $statusLabel = $status == Faq::STATUS_ACTIVE ? 'activated' : 'deactivated';
            Yii::$app->session->setFlash('success', count($ids) . ' FAQs ' . $statusLabel . ' successfully.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the Faq model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Faq the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Faq::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested FAQ does not exist.');
    }
}
