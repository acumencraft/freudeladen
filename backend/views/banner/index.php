<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Banner-Verwaltung';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="banner-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="fas fa-plus me-1"></i>Neuen Banner erstellen', ['create'], [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover'],
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width: 50px;'],
                    ],

                    [
                        'attribute' => 'image_path',
                        'format' => 'html',
                        'label' => 'Bild',
                        'value' => function ($model) {
                            return Html::img($model->getImageUrl(), [
                                'style' => 'max-width: 100px; max-height: 60px; object-fit: cover;',
                                'class' => 'img-thumbnail'
                            ]);
                        },
                        'headerOptions' => ['style' => 'width: 120px;'],
                    ],

                    'title',
                    'subtitle:ntext',
                    
                    [
                        'attribute' => 'position',
                        'value' => function ($model) {
                            $options = \common\models\Banner::getPositionOptions();
                            return $options[$model->position] ?? $model->position;
                        },
                    ],

                    [
                        'attribute' => 'is_active',
                        'format' => 'html',
                        'value' => function ($model) {
                            $isCurrentlyActive = $model->isCurrentlyActive();
                            if ($isCurrentlyActive) {
                                return '<span class="badge bg-success">Aktiv</span>';
                            } else {
                                return '<span class="badge bg-secondary">Inaktiv</span>';
                            }
                        },
                        'headerOptions' => ['style' => 'width: 100px;'],
                    ],

                    [
                        'attribute' => 'start_date',
                        'format' => 'date',
                        'headerOptions' => ['style' => 'width: 120px;'],
                    ],

                    [
                        'attribute' => 'end_date',
                        'format' => 'date',
                        'headerOptions' => ['style' => 'width: 120px;'],
                    ],

                    [
                        'attribute' => 'sort_order',
                        'headerOptions' => ['style' => 'width: 80px;'],
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {toggle} {delete}',
                        'buttons' => [
                            'toggle' => function ($url, $model, $key) {
                                $icon = $model->is_active ? 'fa-toggle-on text-success' : 'fa-toggle-off text-secondary';
                                $title = $model->is_active ? 'Deaktivieren' : 'Aktivieren';
                                return Html::a('<i class="fas ' . $icon . '"></i>', ['toggle-status', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-secondary',
                                    'title' => $title,
                                    'data-method' => 'post',
                                ]);
                            },
                        ],
                        'headerOptions' => ['style' => 'width: 150px;'],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<script>
// Enable sortable for banner order
$(document).ready(function() {
    // You can implement drag-and-drop sorting here if needed
});
</script>
