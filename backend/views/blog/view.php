<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BlogPost */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Blog Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-post-view">

    <div class="row">
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this blog post?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Blog Post Content</h5>
                </div>
                <div class="card-body">
                    <?php if ($model->featured_image): ?>
                        <div class="featured-image mb-4">
                            <?= Html::img($model->getFeaturedImageUrl(), [
                                'style' => 'width: 100%; height: auto; border-radius: 8px;',
                                'alt' => $model->title
                            ]) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($model->excerpt): ?>
                        <div class="excerpt mb-3">
                            <h6>Excerpt:</h6>
                            <p class="text-muted"><?= Html::encode($model->excerpt) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="content">
                        <h6>Content:</h6>
                        <div class="border p-3 bg-light" style="min-height: 200px;">
                            <?= nl2br(Html::encode($model->content)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Post Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Post Information</h6>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'slug',
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $class = $model->status == 1 ? 'success' : 'warning';
                                    return '<span class="badge badge-' . $class . '">' . $model->getStatusLabel() . '</span>';
                                },
                            ],
                            [
                                'attribute' => 'category_id',
                                'label' => 'Category',
                                'value' => $model->category ? $model->category->name : 'No Category',
                            ],
                            'published_at:datetime',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Tags -->
            <?php if ($model->tags): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h6>Tags</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($model->tags as $tag): ?>
                            <span class="badge badge-info mr-1 mb-1"><?= Html::encode($tag->name) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- SEO Information -->
            <div class="card">
                <div class="card-header">
                    <h6>SEO Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label><strong>Meta Title:</strong></label>
                        <p class="text-muted"><?= Html::encode($model->meta_title ?: 'Not set') ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label><strong>Meta Description:</strong></label>
                        <p class="text-muted"><?= Html::encode($model->meta_description ?: 'Not set') ?></p>
                        <?php if ($model->meta_description): ?>
                            <small class="text-info">Length: <?= strlen($model->meta_description) ?> characters</small>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label><strong>URL Preview:</strong></label>
                        <p class="text-primary small">
                            <?= Yii::$app->urlManager->createAbsoluteUrl(['blog/view', 'slug' => $model->slug]) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
