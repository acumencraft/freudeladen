<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Transaction;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $paymentMethods common\models\PaymentMethod[] */

$this->title = 'Transactions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-index">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-plus"></i> Create Transaction', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                        <?= Html::a('<i class="fa fa-bar-chart"></i> Analytics', ['analytics'], ['class' => 'btn btn-info btn-sm']) ?>
                        <?= Html::a('<i class="fa fa-download"></i> Export', ['export'] + Yii::$app->request->queryParams, ['class' => 'btn btn-default btn-sm']) ?>
                    </div>
                </div>

                <!-- Search Form -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php $form = \yii\widgets\ActiveForm::begin([
                                'method' => 'get',
                                'options' => ['class' => 'form-inline search-form'],
                            ]); ?>
                            
                            <div class="form-group">
                                <?= Html::textInput('transaction_id', Yii::$app->request->get('transaction_id'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Transaction ID'
                                ]) ?>
                            </div>
                            
                            <div class="form-group">
                                <?= Html::textInput('order_id', Yii::$app->request->get('order_id'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'Order ID'
                                ]) ?>
                            </div>
                            
                            <div class="form-group">
                                <?= Html::dropDownList('payment_method_id', Yii::$app->request->get('payment_method_id'), 
                                    \yii\helpers\ArrayHelper::map($paymentMethods, 'id', 'name'), [
                                    'class' => 'form-control',
                                    'prompt' => 'All Payment Methods'
                                ]) ?>
                            </div>
                            
                            <div class="form-group">
                                <?= Html::dropDownList('type', Yii::$app->request->get('type'), 
                                    Transaction::getTypeOptions(), [
                                    'class' => 'form-control',
                                    'prompt' => 'All Types'
                                ]) ?>
                            </div>
                            
                            <div class="form-group">
                                <?= Html::dropDownList('status', Yii::$app->request->get('status'), 
                                    Transaction::getStatusOptions(), [
                                    'class' => 'form-control',
                                    'prompt' => 'All Statuses'
                                ]) ?>
                            </div>
                            
                            <div class="form-group">
                                <?= Html::textInput('date_from', Yii::$app->request->get('date_from'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'From Date',
                                    'type' => 'date'
                                ]) ?>
                            </div>
                            
                            <div class="form-group">
                                <?= Html::textInput('date_to', Yii::$app->request->get('date_to'), [
                                    'class' => 'form-control',
                                    'placeholder' => 'To Date',
                                    'type' => 'date'
                                ]) ?>
                            </div>
                            
                            <div class="form-group">
                                <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                                <?= Html::a('<i class="fa fa-refresh"></i> Reset', ['index'], ['class' => 'btn btn-default']) ?>
                            </div>
                            
                            <?php \yii\widgets\ActiveForm::end(); ?>
                        </div>
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
                                'attribute' => 'transaction_id',
                                'format' => 'html',
                                'value' => function ($model) {
                                    return Html::a(Html::encode($model->transaction_id), ['view', 'id' => $model->id], [
                                        'class' => 'text-primary'
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'order_id',
                                'value' => function ($model) {
                                    return $model->order_id ?: '-';
                                },
                            ],
                            [
                                'attribute' => 'paymentMethod.name',
                                'label' => 'Payment Method',
                                'value' => function ($model) {
                                    return $model->paymentMethod ? $model->paymentMethod->name : '-';
                                },
                            ],
                            [
                                'attribute' => 'type',
                                'value' => function ($model) {
                                    return $model->getTypeLabel();
                                },
                                'filter' => Transaction::getTypeOptions(),
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function ($model) {
                                    $class = 'label label-default';
                                    switch ($model->status) {
                                        case Transaction::STATUS_PENDING:
                                            $class = 'label label-warning';
                                            break;
                                        case Transaction::STATUS_COMPLETED:
                                            $class = 'label label-success';
                                            break;
                                        case Transaction::STATUS_FAILED:
                                            $class = 'label label-danger';
                                            break;
                                        case Transaction::STATUS_CANCELLED:
                                            $class = 'label label-default';
                                            break;
                                        case Transaction::STATUS_REFUNDED:
                                            $class = 'label label-info';
                                            break;
                                    }
                                    return '<span class="' . $class . '">' . $model->getStatusLabel() . '</span>';
                                },
                                'filter' => Transaction::getStatusOptions(),
                            ],
                            [
                                'attribute' => 'amount',
                                'format' => ['currency', 'EUR'],
                                'headerOptions' => ['class' => 'text-right'],
                                'contentOptions' => ['class' => 'text-right'],
                            ],
                            [
                                'attribute' => 'fee',
                                'format' => ['currency', 'EUR'],
                                'headerOptions' => ['class' => 'text-right'],
                                'contentOptions' => ['class' => 'text-right'],
                            ],
                            [
                                'label' => 'Net Amount',
                                'format' => ['currency', 'EUR'],
                                'value' => function ($model) {
                                    return $model->getNetAmount();
                                },
                                'headerOptions' => ['class' => 'text-right'],
                                'contentOptions' => ['class' => 'text-right'],
                            ],
                            'created_at:datetime',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {refund} {complete} {fail}',
                                'buttons' => [
                                    'refund' => function ($url, $model, $key) {
                                        if ($model->canBeRefunded()) {
                                            return Html::a('<i class="fa fa-undo"></i>', ['refund', 'id' => $model->id], [
                                                'class' => 'btn btn-warning btn-xs',
                                                'title' => 'Refund',
                                                'data-toggle' => 'tooltip',
                                            ]);
                                        }
                                        return '';
                                    },
                                    'complete' => function ($url, $model, $key) {
                                        if ($model->isPending()) {
                                            return Html::a('<i class="fa fa-check"></i>', ['mark-completed', 'id' => $model->id], [
                                                'class' => 'btn btn-success btn-xs',
                                                'title' => 'Mark Completed',
                                                'data-toggle' => 'tooltip',
                                                'data-confirm' => 'Are you sure you want to mark this transaction as completed?',
                                                'data-method' => 'post',
                                            ]);
                                        }
                                        return '';
                                    },
                                    'fail' => function ($url, $model, $key) {
                                        if ($model->isPending()) {
                                            return Html::a('<i class="fa fa-times"></i>', ['mark-failed', 'id' => $model->id], [
                                                'class' => 'btn btn-danger btn-xs',
                                                'title' => 'Mark Failed',
                                                'data-toggle' => 'tooltip',
                                            ]);
                                        }
                                        return '';
                                    },
                                ],
                                'options' => ['width' => '150px'],
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
                <span class="info-box-icon bg-aqua"><i class="fa fa-exchange"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Transactions</span>
                    <span class="info-box-number"><?= $dataProvider->getTotalCount() ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completed</span>
                    <span class="info-box-number"><?= Transaction::find()->where(['status' => Transaction::STATUS_COMPLETED])->count() ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number"><?= Transaction::find()->where(['status' => Transaction::STATUS_PENDING])->count() ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-exclamation"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Failed</span>
                    <span class="info-box-number"><?= Transaction::find()->where(['status' => Transaction::STATUS_FAILED])->count() ?></span>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.search-form .form-group {
    margin-right: 10px;
    margin-bottom: 10px;
}
.search-form .form-control {
    width: auto;
    display: inline-block;
}
</style>
