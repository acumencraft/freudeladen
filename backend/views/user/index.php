<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap5\ActiveForm;

$this->title = 'Benutzerverwaltung';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="fas fa-download me-1"></i>Export CSV', ['export'], [
                'class' => 'btn btn-outline-success'
            ]) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Search Form -->
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['class' => 'mb-4'],
            ]); ?>
            
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'email')->textInput(['placeholder' => 'E-Mail suchen...']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($searchModel, 'status')->dropDownList([
                        '' => 'Alle Status',
                        '1' => 'Aktiv',
                        '0' => 'Blockiert'
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'created_at_from')->input('date') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'created_at_to')->input('date') ?>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <?= Html::submitButton('<i class="fas fa-search"></i>', [
                            'class' => 'btn btn-primary d-block'
                        ]) ?>
                    </div>
                </div>
            </div>
            
            <?php ActiveForm::end(); ?>

            <!-- Users Grid -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    'email:email',
                    'phone',
                    [
                        'attribute' => 'email_verified',
                        'label' => 'E-Mail verifiziert',
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model->email_verified ? 
                                '<span class="badge bg-success">Ja</span>' : 
                                '<span class="badge bg-warning">Nein</span>';
                        },
                    ],
                    [
                        'attribute' => 'phone_verified',
                        'label' => 'Telefon verifiziert',
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model->phone_verified ? 
                                '<span class="badge bg-success">Ja</span>' : 
                                '<span class="badge bg-warning">Nein</span>';
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model->status ? 
                                '<span class="badge bg-success">Aktiv</span>' : 
                                '<span class="badge bg-danger">Blockiert</span>';
                        },
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'label' => 'Registriert am',
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {block}',
                        'buttons' => [
                            'block' => function ($url, $model, $key) {
                                if ($model->status) {
                                    return Html::a('<i class="fas fa-ban"></i>', ['block', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'title' => 'Blockieren',
                                        'data-confirm' => 'Sind Sie sicher, dass Sie diesen Benutzer blockieren möchten?',
                                        'data-method' => 'post',
                                    ]);
                                } else {
                                    return Html::a('<i class="fas fa-check"></i>', ['unblock', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-outline-success',
                                        'title' => 'Entsperren',
                                        'data-confirm' => 'Sind Sie sicher, dass Sie diesen Benutzer entsperren möchten?',
                                        'data-method' => 'post',
                                    ]);
                                }
                            },
                        ],
                        'headerOptions' => ['style' => 'width: 150px;'],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
