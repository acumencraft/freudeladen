<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'SEO Pages Management';
$this->params['breadcrumbs'][] = ['label' => 'SEO Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seo-pages">
    
    <!-- Header with Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt"></i> SEO Pages Management
                        </h5>
                        <div class="btn-group">
                            <?= Html::a('<i class="fas fa-plus"></i> Add New Page', ['create-page'], ['class' => 'btn btn-light btn-sm']) ?>
                            <?= Html::a('<i class="fas fa-magic"></i> Auto-detect Pages', ['auto-detect-pages'], ['class' => 'btn btn-warning btn-sm']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Pages Grid -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    
                    <?php Pjax::begin(); ?>
                    
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-striped table-bordered'],
                        'summary' => '<div class="summary-info">Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> SEO pages</div>',
                        'columns' => [
                            [
                                'attribute' => 'route',
                                'label' => 'Route/URL',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<code>' . Html::encode($model->route) . '</code>';
                                },
                                'headerOptions' => ['style' => 'width: 200px;'],
                            ],
                            [
                                'attribute' => 'title',
                                'label' => 'Page Title',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $title = Html::encode($model->title);
                                    if (strlen($title) > 50) {
                                        return substr($title, 0, 50) . '...';
                                    }
                                    return $title ?: '<span class="text-muted">No title</span>';
                                },
                            ],
                            [
                                'attribute' => 'description',
                                'label' => 'Meta Description',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->description) {
                                        $desc = Html::encode($model->description);
                                        if (strlen($desc) > 80) {
                                            return substr($desc, 0, 80) . '...';
                                        }
                                        return $desc;
                                    }
                                    return '<span class="text-muted">No description</span>';
                                },
                            ],
                            [
                                'attribute' => 'priority',
                                'label' => 'Priority',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $badgeClass = 'secondary';
                                    if ($model->priority >= 0.8) {
                                        $badgeClass = 'success';
                                    } elseif ($model->priority >= 0.5) {
                                        $badgeClass = 'info';
                                    } elseif ($model->priority >= 0.3) {
                                        $badgeClass = 'warning';
                                    }
                                    return '<span class="badge badge-' . $badgeClass . '">' . $model->priority . '</span>';
                                },
                                'headerOptions' => ['style' => 'width: 80px;'],
                            ],
                            [
                                'attribute' => 'changefreq',
                                'label' => 'Change Freq',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->changefreq) {
                                        return '<span class="badge badge-outline-primary">' . Html::encode($model->getChangeFreqLabel()) . '</span>';
                                    }
                                    return '<span class="text-muted">Not set</span>';
                                },
                                'headerOptions' => ['style' => 'width: 100px;'],
                            ],
                            [
                                'attribute' => 'robots',
                                'label' => 'Robots',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->robots) {
                                        $robotsClass = strpos($model->robots, 'noindex') !== false ? 'danger' : 'success';
                                        return '<span class="badge badge-' . $robotsClass . '">' . Html::encode($model->robots) . '</span>';
                                    }
                                    return '<span class="text-muted">Default</span>';
                                },
                                'headerOptions' => ['style' => 'width: 120px;'],
                            ],
                            [
                                'attribute' => 'updated_at',
                                'label' => 'Last Updated',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return '<small>' . Yii::$app->formatter->asRelativeTime($model->updated_at) . '</small>';
                                },
                                'headerOptions' => ['style' => 'width: 120px;'],
                            ],
                            [
                                'attribute' => 'is_active',
                                'label' => 'Status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->is_active) {
                                        return '<span class="badge badge-success">Active</span>';
                                    }
                                    return '<span class="badge badge-secondary">Inactive</span>';
                                },
                                'headerOptions' => ['style' => 'width: 80px;'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Actions',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-eye"></i>', $model->getFullUrl(), [
                                            'class' => 'btn btn-sm btn-outline-info',
                                            'title' => 'View Page',
                                            'target' => '_blank',
                                            'data-toggle' => 'tooltip'
                                        ]);
                                    },
                                    'update' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-edit"></i>', ['update-page', 'id' => $model->id], [
                                            'class' => 'btn btn-sm btn-outline-primary',
                                            'title' => 'Edit SEO Settings',
                                            'data-toggle' => 'tooltip'
                                        ]);
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-trash"></i>', ['delete-page', 'id' => $model->id], [
                                            'class' => 'btn btn-sm btn-outline-danger',
                                            'title' => 'Delete',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Are you sure you want to delete this SEO page?',
                                            'data-toggle' => 'tooltip'
                                        ]);
                                    },
                                ],
                                'headerOptions' => ['style' => 'width: 120px;'],
                            ],
                        ],
                    ]); ?>
                    
                    <?php Pjax::end(); ?>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Tips -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-info">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-lightbulb text-warning"></i> SEO Tips & Best Practices
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-star text-success"></i> Page Titles</h6>
                            <ul class="small">
                                <li>Keep titles under 60 characters</li>
                                <li>Include target keywords</li>
                                <li>Make them unique and descriptive</li>
                                <li>Include brand name</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-star text-info"></i> Meta Descriptions</h6>
                            <ul class="small">
                                <li>Keep between 150-160 characters</li>
                                <li>Write compelling copy</li>
                                <li>Include call-to-action</li>
                                <li>Avoid duplicate descriptions</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-star text-warning"></i> Sitemap Priority</h6>
                            <ul class="small">
                                <li><strong>1.0:</strong> Homepage, main category pages</li>
                                <li><strong>0.8:</strong> Important product/service pages</li>
                                <li><strong>0.6:</strong> Regular content pages</li>
                                <li><strong>0.4:</strong> Archive, tag pages</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.summary-info {
    padding: 10px 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.btn-sm {
    margin: 1px;
}

.badge {
    font-size: 0.75rem;
}

.badge-outline-primary {
    color: #007bff;
    background-color: transparent;
    border: 1px solid #007bff;
}

code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.85rem;
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0;
}

[data-toggle="tooltip"] {
    cursor: pointer;
}
</style>

<?php
$this->registerJs("
// Initialize tooltips
$('[data-toggle=\"tooltip\"]').tooltip();
");
?>
