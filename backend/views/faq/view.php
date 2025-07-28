<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Faq */

$this->title = $model->question;
$this->params['breadcrumbs'][] = ['label' => 'FAQs', 'url' => ['index']];
$this->params['breadcrumbs'][] = substr($this->title, 0, 50) . '...';
?>

<div class="faq-view">

    <div class="row">
        <div class="col-md-8">
            <h1><?= Html::encode(substr($this->title, 0, 100) . (strlen($this->title) > 100 ? '...' : '')) ?></h1>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this FAQ?',
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
                    <h5>Question & Answer</h5>
                </div>
                <div class="card-body">
                    <div class="question mb-4">
                        <h6 class="text-primary">Question:</h6>
                        <p class="lead"><?= Html::encode($model->question) ?></p>
                    </div>

                    <div class="answer">
                        <h6 class="text-success">Answer:</h6>
                        <div class="border p-3 bg-light" style="min-height: 100px;">
                            <?= nl2br(Html::encode($model->answer)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- FAQ Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>FAQ Information</h6>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'category_id',
                                'label' => 'Category',
                                'value' => $model->category ? $model->category->name : 'No Category',
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $class = $model->status == 1 ? 'success' : 'secondary';
                                    return '<span class="badge badge-' . $class . '">' . $model->getStatusLabel() . '</span>';
                                },
                            ],
                            'sort_order',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= Html::a(
                            $model->status == 1 ? 'Deactivate FAQ' : 'Activate FAQ',
                            ['toggle-status', 'id' => $model->id],
                            [
                                'class' => 'btn btn-outline-' . ($model->status == 1 ? 'warning' : 'success') . ' btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => 'Are you sure you want to change the status of this FAQ?'
                            ]
                        ) ?>
                        
                        <?php if ($model->category): ?>
                            <?= Html::a('View Category', ['faq-category/view', 'id' => $model->category->id], [
                                'class' => 'btn btn-outline-info btn-sm'
                            ]) ?>
                        <?php endif; ?>
                        
                        <?= Html::a('View All FAQs', ['index', 'category_id' => $model->category_id], [
                            'class' => 'btn btn-outline-secondary btn-sm'
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Category Information -->
            <?php if ($model->category): ?>
                <div class="card">
                    <div class="card-header">
                        <h6>Category: <?= Html::encode($model->category->name) ?></h6>
                    </div>
                    <div class="card-body">
                        <?php if ($model->category->description): ?>
                            <p class="text-muted small"><?= Html::encode($model->category->description) ?></p>
                        <?php endif; ?>
                        
                        <div class="text-center">
                            <span class="badge badge-info">
                                <?= $model->category->getActiveFaqCount() ?> Active FAQs
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
