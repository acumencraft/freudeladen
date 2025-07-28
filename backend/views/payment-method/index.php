<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Methods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-method-index">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-plus"></i> Create Payment Method', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                        <?= Html::a('<i class="fa fa-cogs"></i> Test Payment Methods', ['test'], ['class' => 'btn btn-info btn-sm']) ?>
                    </div>
                </div>

                <div class="box-body">
                    <?php Pjax::begin(); ?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-striped table-bordered'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            [
                                'attribute' => 'name',
                                'format' => 'html',
                                'value' => function ($model) {
                                    $html = Html::encode($model->name);
                                    if ($model->description) {
                                        $html .= '<br><small class="text-muted">' . Html::encode($model->description) . '</small>';
                                    }
                                    return $html;
                                },
                            ],
                            [
                                'attribute' => 'type',
                                'value' => function ($model) {
                                    return $model->getTypeLabel();
                                },
                                'filter' => PaymentMethod::getTypeOptions(),
                            ],
                            [
                                'attribute' => 'provider',
                                'value' => function ($model) {
                                    return $model->getProviderLabel();
                                },
                                'filter' => PaymentMethod::getProviderOptions(),
                            ],
                            [
                                'attribute' => 'fee_type',
                                'format' => 'html',
                                'value' => function ($model) {
                                    $feeHtml = $model->getFeeTypeLabel();
                                    if ($model->fee_fixed > 0) {
                                        $feeHtml .= '<br><small>Fixed: ' . Yii::$app->formatter->asCurrency($model->fee_fixed, 'EUR') . '</small>';
                                    }
                                    if ($model->fee_percentage > 0) {
                                        $feeHtml .= '<br><small>Percentage: ' . $model->fee_percentage . '%</small>';
                                    }
                                    return $feeHtml;
                                },
                            ],
                            [
                                'attribute' => 'is_active',
                                'format' => 'html',
                                'value' => function ($model) {
                                    return $model->is_active ? 
                                        '<span class="label label-success">Active</span>' : 
                                        '<span class="label label-danger">Inactive</span>';
                                },
                                'filter' => [1 => 'Active', 0 => 'Inactive'],
                            ],
                            [
                                'attribute' => 'sort_order',
                                'options' => ['width' => '80px'],
                            ],
                            'created_at:datetime',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {delete} {toggle}',
                                'buttons' => [
                                    'toggle' => function ($url, $model, $key) {
                                        $title = $model->is_active ? 'Deactivate' : 'Activate';
                                        $icon = $model->is_active ? 'fa-toggle-off' : 'fa-toggle-on';
                                        $class = $model->is_active ? 'btn-warning' : 'btn-success';
                                        
                                        return Html::a('<i class="fa ' . $icon . '"></i>', ['toggle-status', 'id' => $model->id], [
                                            'class' => 'btn btn-xs ' . $class,
                                            'title' => $title,
                                            'data-confirm' => 'Are you sure you want to ' . strtolower($title) . ' this payment method?',
                                            'data-method' => 'post',
                                        ]);
                                    },
                                ],
                                'options' => ['width' => '120px'],
                            ],
                        ],
                    ]); ?>

                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-credit-card"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Methods</span>
                    <span class="info-box-number"><?= $dataProvider->getTotalCount() ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Methods</span>
                    <span class="info-box-number"><?= PaymentMethod::find()->andWhere(['is_active' => 1])->count() ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-pause"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Inactive Methods</span>
                    <span class="info-box-number"><?= PaymentMethod::find()->andWhere(['is_active' => 0])->count() ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-euro"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Avg Fee Rate</span>
                    <span class="info-box-number"><?= number_format(PaymentMethod::find()->average('fee_percentage'), 1) ?>%</span>
                </div>
            </div>
        </div>
    </div>

</div>
