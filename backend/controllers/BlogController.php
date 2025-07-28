<?php

namespace backend\controllers;

use Yii;
use common\models\BlogPost;
use common\models\BlogCategory;
use common\models\BlogTag;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all BlogPost models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BlogPost();
        
        $query = BlogPost::find()->with(['category']);
        
        // Search functionality
        if (Yii::$app->request->get('title')) {
            $query->andWhere(['like', 'title', Yii::$app->request->get('title')]);
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
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $categories = ArrayHelper::map(BlogCategory::find()->all(), 'id', 'name');

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'categories' => $categories,
        ]);
    }

    /**
     * Displays a single BlogPost model.
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
     * Creates a new BlogPost model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BlogPost();
        
        if ($model->load(Yii::$app->request->post())) {
            
            // Generate slug if not provided
            if (empty($model->slug)) {
                $model->slug = BlogPost::generateSlug($model->title);
            }
            
            // Handle file upload
            $uploadedFile = UploadedFile::getInstance($model, 'featured_image');
            if ($uploadedFile) {
                $fileName = time() . '_' . $uploadedFile->baseName . '.' . $uploadedFile->extension;
                $uploadPath = Yii::getAlias('@webroot') . '/uploads/blog/';
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                if ($uploadedFile->saveAs($uploadPath . $fileName)) {
                    $model->featured_image = $fileName;
                }
            }
            
            if ($model->save()) {
                
                // Handle tags
                $tags = Yii::$app->request->post('tags', []);
                if (!empty($tags)) {
                    foreach ($tags as $tagId) {
                        Yii::$app->db->createCommand()->insert('blog_post_tag', [
                            'post_id' => $model->id,
                            'tag_id' => $tagId
                        ])->execute();
                    }
                }
                
                Yii::$app->session->setFlash('success', 'Blog post created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => ArrayHelper::map(BlogCategory::find()->all(), 'id', 'name'),
            'tags' => ArrayHelper::map(BlogTag::find()->all(), 'id', 'name'),
        ]);
    }

    /**
     * Updates an existing BlogPost model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->featured_image;
        
        if ($model->load(Yii::$app->request->post())) {
            
            // Handle file upload
            $uploadedFile = UploadedFile::getInstance($model, 'featured_image');
            if ($uploadedFile) {
                $fileName = time() . '_' . $uploadedFile->baseName . '.' . $uploadedFile->extension;
                $uploadPath = Yii::getAlias('@webroot') . '/uploads/blog/';
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                if ($uploadedFile->saveAs($uploadPath . $fileName)) {
                    // Delete old image
                    if ($oldImage && file_exists($uploadPath . $oldImage)) {
                        unlink($uploadPath . $oldImage);
                    }
                    $model->featured_image = $fileName;
                } else {
                    $model->featured_image = $oldImage;
                }
            } else {
                $model->featured_image = $oldImage;
            }
            
            if ($model->save()) {
                
                // Update tags
                Yii::$app->db->createCommand()->delete('blog_post_tag', ['post_id' => $model->id])->execute();
                
                $tags = Yii::$app->request->post('tags', []);
                if (!empty($tags)) {
                    foreach ($tags as $tagId) {
                        Yii::$app->db->createCommand()->insert('blog_post_tag', [
                            'post_id' => $model->id,
                            'tag_id' => $tagId
                        ])->execute();
                    }
                }
                
                Yii::$app->session->setFlash('success', 'Blog post updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        // Get current tags
        $currentTags = ArrayHelper::getColumn($model->tags, 'id');

        return $this->render('update', [
            'model' => $model,
            'categories' => ArrayHelper::map(BlogCategory::find()->all(), 'id', 'name'),
            'tags' => ArrayHelper::map(BlogTag::find()->all(), 'id', 'name'),
            'currentTags' => $currentTags,
        ]);
    }

    /**
     * Deletes an existing BlogPost model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Delete featured image
        if ($model->featured_image) {
            $imagePath = Yii::getAlias('@webroot') . '/uploads/blog/' . $model->featured_image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Blog post deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Toggle status of a blog post
     * @param integer $id
     * @return mixed
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == BlogPost::STATUS_PUBLISHED ? BlogPost::STATUS_DRAFT : BlogPost::STATUS_PUBLISHED;
        
        if ($model->status == BlogPost::STATUS_PUBLISHED && empty($model->published_at)) {
            $model->published_at = date('Y-m-d H:i:s');
        }
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Status updated successfully.');
        }
        
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
            $posts = BlogPost::find()->where(['id' => $ids])->all();
            foreach ($posts as $post) {
                // Delete featured image
                if ($post->featured_image) {
                    $imagePath = Yii::getAlias('@webroot') . '/uploads/blog/' . $post->featured_image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $post->delete();
            }
            Yii::$app->session->setFlash('success', count($ids) . ' blog posts deleted successfully.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the BlogPost model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BlogPost the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogPost::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested blog post does not exist.');
    }
}
