<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Bestellungen';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'order_number',
                'value' => function($model) {
                    return Html::a($model->order_number, ['view', 'id' => $model->id], ['class' => 'text-decoration-none']);
                },
                'format' => 'raw',
                'label' => 'Bestell-Nr.'
            ],
            'customer_name',
            'customer_email:email',
            [
                'attribute' => 'total_amount',
                'value' => function($model) {
                    return '€' . number_format($model->total_amount, 2);
                },
                'label' => 'Gesamt'
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    $statusLabels = [
                        'pending' => ['label' => 'Ausstehend', 'class' => 'warning'],
                        'processing' => ['label' => 'In Bearbeitung', 'class' => 'info'],
                        'shipped' => ['label' => 'Versandt', 'class' => 'primary'],
                        'delivered' => ['label' => 'Geliefert', 'class' => 'success'],
                        'cancelled' => ['label' => 'Storniert', 'class' => 'danger'],
                    ];
                    $status = $statusLabels[$model->status] ?? ['label' => $model->status, 'class' => 'secondary'];
                    return '<span class="badge bg-' . $status['class'] . '">' . $status['label'] . '</span>';
                },
                'format' => 'raw',
                'label' => 'Status'
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'label' => 'Bestellt am'
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, [
                            'title' => 'Anzeigen',
                            'class' => 'btn btn-sm btn-outline-info'
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-edit"></i>', $url, [
                            'title' => 'Bearbeiten',
                            'class' => 'btn btn-sm btn-outline-primary'
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>

</div>
