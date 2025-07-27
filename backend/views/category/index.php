<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Kategorien';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Neue Kategorie erstellen', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'slug',
            [
                'attribute' => 'parent_id',
                'value' => function($model) {
                    return $model->parent ? $model->parent->name : 'Hauptkategorie';
                },
                'label' => 'Übergeordnete Kategorie'
            ],
            'sort_order',
            [
                'attribute' => 'is_active',
                'format' => 'boolean',
                'label' => 'Aktiv'
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'label' => 'Erstellt am'
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
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
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                            'title' => 'Löschen',
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data' => [
                                'confirm' => 'Sind Sie sicher, dass Sie diese Kategorie löschen möchten?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                ]
            ],
        ],
    ]); ?>

</div>
