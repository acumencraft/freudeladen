<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\BlogPost;
use app\models\BlogCategory;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * BlogController implements the CRUD actions for BlogPost model.
 */
class BlogController extends Controller
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
     * Lists all BlogPost models.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => BlogPost::find()->with(['category']),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
        ]);

        // Filter by category
        $categoryId = Yii::$app->request->get('category_id');
        if ($categoryId) {
            $dataProvider->query->andWhere(['category_id' => $categoryId]);
        }

        // Filter by status
        $status = Yii::$app->request->get('status');
        if ($status !== null) {
            $dataProvider->query->andWhere(['status' => $status]);
        }

        // Search
        $search = Yii::$app->request->get('search');
        if ($search) {
            $dataProvider->query->andWhere([
                'or',
                ['like', 'title', $search],
                ['like', 'content', $search],
                ['like', 'excerpt', $search]
            ]);
        }

        $categories = BlogCategory::find()->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'categories' => $categories,
            'currentCategory' => $categoryId,
            'currentStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Displays a single BlogPost model.
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
     * Creates a new BlogPost model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new BlogPost();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Handle featured image upload
                $model->featured_image = UploadedFile::getInstance($model, 'featured_image');
                
                // Set author to current user
                $model->author_id = Yii::$app->user->id;
                
                if ($model->save()) {
                    // Handle image upload
                    if ($model->featured_image) {
                        $imagePath = 'uploads/blog/' . $model->id . '_' . time() . '.' . $model->featured_image->extension;
                        $model->featured_image->saveAs($imagePath);
                        $model->featured_image = $imagePath;
                        $model->save(false);
                    }
                    
                    Yii::$app->session->setFlash('success', 'Blog post created successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
            $model->status = BlogPost::STATUS_DRAFT;
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BlogPost model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->featured_image;

        if ($this->request->isPost && $model->load($this->request->post())) {
            // Handle featured image upload
            $uploadedImage = UploadedFile::getInstance($model, 'featured_image');
            
            if ($uploadedImage) {
                // Delete old image
                if ($oldImage && file_exists($oldImage)) {
                    unlink($oldImage);
                }
                
                // Save new image
                $imagePath = 'uploads/blog/' . $model->id . '_' . time() . '.' . $uploadedImage->extension;
                $uploadedImage->saveAs($imagePath);
                $model->featured_image = $imagePath;
            } else {
                // Keep old image
                $model->featured_image = $oldImage;
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Blog post updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BlogPost model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Delete featured image
        if ($model->featured_image && file_exists($model->featured_image)) {
            unlink($model->featured_image);
        }
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Blog post deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Toggle the status of a blog post.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        
        $model->status = $model->status === BlogPost::STATUS_PUBLISHED 
            ? BlogPost::STATUS_DRAFT 
            : BlogPost::STATUS_PUBLISHED;
            
        if ($model->save()) {
            $statusText = $model->status === BlogPost::STATUS_PUBLISHED ? 'published' : 'drafted';
            Yii::$app->session->setFlash('success', "Blog post {$statusText} successfully.");
        } else {
            Yii::$app->session->setFlash('error', 'Failed to update status.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Bulk actions for blog posts.
     * @return Response
     */
    public function actionBulkAction()
    {
        $action = Yii::$app->request->post('action');
        $ids = Yii::$app->request->post('ids', []);

        if (!$action || empty($ids)) {
            Yii::$app->session->setFlash('error', 'Please select posts and action.');
            return $this->redirect(['index']);
        }

        $count = 0;
        
        switch ($action) {
            case 'publish':
                $count = BlogPost::updateAll(['status' => BlogPost::STATUS_PUBLISHED], ['id' => $ids]);
                Yii::$app->session->setFlash('success', "{$count} posts published successfully.");
                break;
                
            case 'draft':
                $count = BlogPost::updateAll(['status' => BlogPost::STATUS_DRAFT], ['id' => $ids]);
                Yii::$app->session->setFlash('success', "{$count} posts drafted successfully.");
                break;
                
            case 'delete':
                // Delete featured images first
                $posts = BlogPost::find()->where(['id' => $ids])->all();
                foreach ($posts as $post) {
                    if ($post->featured_image && file_exists($post->featured_image)) {
                        unlink($post->featured_image);
                    }
                }
                $count = BlogPost::deleteAll(['id' => $ids]);
                Yii::$app->session->setFlash('success', "{$count} posts deleted successfully.");
                break;
                
            default:
                Yii::$app->session->setFlash('error', 'Invalid action selected.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Get blog statistics for dashboard.
     * @return Response
     */
    public function actionStatistics()
    {
        $statistics = [
            'total_posts' => BlogPost::find()->count(),
            'published_posts' => BlogPost::find()->where(['status' => BlogPost::STATUS_PUBLISHED])->count(),
            'draft_posts' => BlogPost::find()->where(['status' => BlogPost::STATUS_DRAFT])->count(),
            'recent_posts' => BlogPost::find()
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(5)
                ->all(),
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $statistics;
    }

    /**
     * Finds the BlogPost model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return BlogPost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogPost::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
