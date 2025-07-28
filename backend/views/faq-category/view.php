<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\FaqCategory */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'FAQ Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Create data provider for FAQs in this category
$faqsDataProvider = new ActiveDataProvider([
    'query' => $model->getFaqs()->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]),
    'pagination' => [
        'pageSize' => 10,
    ],
]);
?>

<div class="faq-category-view">

    <div class="row">
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-4 text-right">
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php if ($model->getFaqCount() == 0): ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this FAQ category?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php else: ?>
                <?= Html::button('Delete', [
                    'class' => 'btn btn-danger disabled',
                    'title' => 'Cannot delete category with FAQs',
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
                            'description:ntext',
                            'sort_order',
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $class = $model->status == 1 ? 'success' : 'secondary';
                                    return '<span class="badge badge-' . $class . '">' . $model->getStatusLabel() . '</span>';
                                },
                            ],
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
                    <h5>Statistics & Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3 class="text-primary"><?= $model->getFaqCount() ?></h3>
                                <p class="text-muted">Total FAQs</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3 class="text-success"><?= $model->getActiveFaqCount() ?></h3>
                                <p class="text-muted">Active FAQs</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <?= Html::a('Create New FAQ', ['/faq/create', 'category_id' => $model->id], [
                            'class' => 'btn btn-success btn-sm'
                        ]) ?>
                        
                        <?= Html::a('View All FAQs in Category', ['/faq/index', 'category_id' => $model->id], [
                            'class' => 'btn btn-outline-primary btn-sm'
                        ]) ?>
                        
                        <?= Html::a(
                            $model->status == 1 ? 'Deactivate Category' : 'Activate Category',
                            ['toggle-status', 'id' => $model->id],
                            [
                                'class' => 'btn btn-outline-' . ($model->status == 1 ? 'warning' : 'success') . ' btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => 'Are you sure you want to change the status of this category?'
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($model->getFaqCount() > 0): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>FAQs in this Category</h5>
                    </div>
                    <div class="card-body">
                        <?= GridView::widget([
                            'dataProvider' => $faqsDataProvider,
                            'columns' => [
                                [
                                    'attribute' => 'sort_order',
                                    'headerOptions' => ['style' => 'width: 80px;'],
                                    'contentOptions' => ['class' => 'text-center'],
                                ],
                                
                                [
                                    'attribute' => 'question',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $question = Html::encode($model->question);
                                        if (strlen($question) > 80) {
                                            $question = substr($question, 0, 80) . '...';
                                        }
                                        return Html::a($question, ['/faq/view', 'id' => $model->id], [
                                            'class' => 'fw-bold text-decoration-none',
                                            'title' => Html::encode($model->question)
                                        ]);
                                    },
                                ],
                                
                                [
                                    'attribute' => 'status',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $class = $model->status == 1 ? 'success' : 'secondary';
                                        return '<span class="badge badge-' . $class . '">' . $model->getStatusLabel() . '</span>';
                                    },
                                    'headerOptions' => ['style' => 'width: 100px;'],
                                ],
                                
                                [
                                    'attribute' => 'created_at',
                                    'format' => 'datetime',
                                    'headerOptions' => ['style' => 'width: 150px;'],
                                ],

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'controller' => 'faq',
                                    'template' => '{view} {update}',
                                    'buttons' => [
                                        'view' => function ($url, $model, $key) {
                                            return Html::a('<i class="fas fa-eye"></i>', ['/faq/view', 'id' => $model->id], [
                                                'title' => 'View',
                                                'class' => 'btn btn-sm btn-outline-primary',
                                            ]);
                                        },
                                        'update' => function ($url, $model, $key) {
                                            return Html::a('<i class="fas fa-edit"></i>', ['/faq/update', 'id' => $model->id], [
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
    <?php else: ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-muted">No FAQs in this category yet</h5>
                        <p class="text-muted">Start by creating your first FAQ for this category.</p>
                        <?= Html::a('Create First FAQ', ['/faq/create', 'category_id' => $model->id], [
                            'class' => 'btn btn-success'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
