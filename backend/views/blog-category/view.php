<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\BlogCategory */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Blog Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Create data provider for posts in this category
$postsDataProvider = new ActiveDataProvider([
    'query' => $model->getBlogPosts()->orderBy(['created_at' => SORT_DESC]),
    'pagination' => [
        'pageSize' => 10,
    ],
]);
?>

<div class="blog-category-view">

    <div class="row">
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php if ($model->getPostCount() == 0): ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this blog category?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php else: ?>
                <?= Html::button('Delete', [
                    'class' => 'btn btn-danger disabled',
                    'title' => 'Cannot delete category with posts',
                    'disabled' => true
                ]) ?>
            <?php endif; ?>
            <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Category Information</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'name',
                            'slug',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3 class="text-primary"><?= $model->getPostCount() ?></h3>
                                <p class="text-muted">Total Posts</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3 class="text-success">
                                    <?= $model->getBlogPosts()->where(['status' => 1])->count() ?>
                                </h3>
                                <p class="text-muted">Published Posts</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <?= Html::a('View All Posts', ['/blog/index', 'category_id' => $model->id], [
                            'class' => 'btn btn-outline-primary btn-sm'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($model->getPostCount() > 0): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Posts in this Category</h5>
                    </div>
                    <div class="card-body">
                        <?= GridView::widget([
                            'dataProvider' => $postsDataProvider,
                            'columns' => [
                                [
                                    'attribute' => 'title',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return Html::a(Html::encode($model->title), ['/blog/view', 'id' => $model->id], [
                                            'class' => 'fw-bold text-decoration-none'
                                        ]);
                                    },
                                ],
                                
                                [
                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $class = $model->status == 1 ? 'success' : 'warning';
                                        return '<span class="badge badge-' . $class . '">' . $model->getStatusLabel() . '</span>';
                                    },
                                    'headerOptions' => ['style' => 'width: 100px;'],
                                ],
                                
                                [
                                    'attribute' => 'published_at',
                                    'format' => 'datetime',
                                    'headerOptions' => ['style' => 'width: 150px;'],
                                ],
                                
                                [
                                    'attribute' => 'created_at',
                                    'format' => 'datetime',
                                    'headerOptions' => ['style' => 'width: 150px;'],
                                ],

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'controller' => 'blog',
                                    'template' => '{view} {update}',
                                    'buttons' => [
                                        'view' => function ($url, $model, $key) {
                                            return Html::a('<i class="fas fa-eye"></i>', ['/blog/view', 'id' => $model->id], [
                                                'title' => 'View',
                                                'class' => 'btn btn-sm btn-outline-primary',
                                            ]);
                                        },
                                        'update' => function ($url, $model, $key) {
                                            return Html::a('<i class="fas fa-edit"></i>', ['/blog/update', 'id' => $model->id], [
                                                'title' => 'Update',
                                                'class' => 'btn btn-sm btn-outline-warning',
                                            ]);
                                        },
                                    ],
                                    'headerOptions' => ['style' => 'width: 100px;'],
                                ],
                            ],
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
