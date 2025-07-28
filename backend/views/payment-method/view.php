<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethod */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Payment Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="payment-method-view">

    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                    <div class="box-tools pull-right">
                        <?= Html::a('<i class="fa fa-pencil"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= Html::a('<i class="fa fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this payment method?',
                                'method' => 'post',
                            ],
                        ]) ?>
                        <?php if ($model->is_active): ?>
                            <?= Html::a('<i class="fa fa-toggle-off"></i> Deactivate', ['toggle-status', 'id' => $model->id], [
                                'class' => 'btn btn-warning btn-sm',
                                'data' => [
                                    'confirm' => 'Are you sure you want to deactivate this payment method?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        <?php else: ?>
                            <?= Html::a('<i class="fa fa-toggle-on"></i> Activate', ['toggle-status', 'id' => $model->id], [
                                'class' => 'btn btn-success btn-sm',
                                'data' => [
                                    'confirm' => 'Are you sure you want to activate this payment method?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped table-bordered detail-view'],
                        'attributes' => [
                            'id',
                            'name',
                            'description:ntext',
                            [
                                'attribute' => 'type',
                                'value' => $model->getTypeLabel(),
                            ],
                            [
                                'attribute' => 'provider',
                                'value' => $model->getProviderLabel(),
                            ],
                            [
                                'attribute' => 'fee_type',
                                'value' => $model->getFeeTypeLabel(),
                            ],
                            [
                                'attribute' => 'fee_fixed',
                                'format' => ['currency', 'EUR'],
                            ],
                            [
                                'attribute' => 'fee_percentage',
                                'format' => 'percent',
                                'value' => $model->fee_percentage / 100,
                            ],
                            [
                                'attribute' => 'min_amount',
                                'format' => ['currency', 'EUR'],
                            ],
                            [
                                'attribute' => 'max_amount',
                                'format' => ['currency', 'EUR'],
                            ],
                            [
                                'attribute' => 'supported_currencies',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $currencies = $model->getSupportedCurrenciesArray();
                                    $html = '';
                                    foreach ($currencies as $currency) {
                                        $html .= '<span class="label label-info" style="margin-right: 5px;">' . Html::encode($currency) . '</span>';
                                    }
                                    return $html ?: '<span class="text-muted">None specified</span>';
                                },
                            ],
                            [
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => $model->is_active ? 
                                    '<span class="label label-success">Active</span>' : 
                                    '<span class="label label-danger">Inactive</span>',
                            ],
                            'sort_order',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Configuration Details -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Configuration</h3>
                </div>
                <div class="box-body">
                    <?php if ($model->config): ?>
                        <?php $config = $model->getConfigArray(); ?>
                        <dl class="dl-horizontal">
                            <?php foreach ($config as $key => $value): ?>
                                <dt><?= Html::encode($key) ?>:</dt>
                                <dd>
                                    <?php if (strpos($key, 'secret') !== false || strpos($key, 'key') !== false): ?>
                                        <code>****</code>
                                    <?php else: ?>
                                        <code><?= Html::encode($value) ?></code>
                                    <?php endif; ?>
                                </dd>
                            <?php endforeach; ?>
                        </dl>
                    <?php else: ?>
                        <p class="text-muted">No configuration data available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Fee Calculation Preview -->
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Fee Calculator</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="fee-amount">Transaction Amount (EUR):</label>
                        <input type="number" id="fee-amount" class="form-control" value="100" step="0.01" min="0">
                    </div>
                    <div class="fee-result">
                        <strong>Fee: <span id="calculated-fee">-</span></strong><br>
                        <small class="text-muted">Net Amount: <span id="net-amount">-</span></small>
                    </div>
                </div>
            </div>

            <!-- Transaction Statistics -->
            <?php
            $transactionCount = \common\models\Transaction::find()
                ->andWhere(['payment_method_id' => $model->id])
                ->count();
            
            $completedTransactions = \common\models\Transaction::find()
                ->andWhere(['payment_method_id' => $model->id, 'status' => \common\models\Transaction::STATUS_COMPLETED])
                ->count();
            
            $totalAmount = \common\models\Transaction::find()
                ->andWhere(['payment_method_id' => $model->id, 'status' => \common\models\Transaction::STATUS_COMPLETED])
                ->sum('amount');
            
            $totalFees = \common\models\Transaction::find()
                ->andWhere(['payment_method_id' => $model->id, 'status' => \common\models\Transaction::STATUS_COMPLETED])
                ->sum('fee');
            ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Transaction Statistics</h3>
                </div>
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt>Total Transactions:</dt>
                        <dd><?= number_format($transactionCount) ?></dd>
                        
                        <dt>Completed:</dt>
                        <dd><?= number_format($completedTransactions) ?></dd>
                        
                        <dt>Success Rate:</dt>
                        <dd>
                            <?php if ($transactionCount > 0): ?>
                                <?= number_format(($completedTransactions / $transactionCount) * 100, 1) ?>%
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </dd>
                        
                        <dt>Total Amount:</dt>
                        <dd><?= Yii::$app->formatter->asCurrency($totalAmount ?: 0, 'EUR') ?></dd>
                        
                        <dt>Total Fees:</dt>
                        <dd><?= Yii::$app->formatter->asCurrency($totalFees ?: 0, 'EUR') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
$this->registerJs("
function calculateFee() {
    var amount = parseFloat($('#fee-amount').val()) || 0;
    var feeFixed = " . $model->fee_fixed . ";
    var feePercentage = " . $model->fee_percentage . ";
    var feeType = '" . $model->fee_type . "';
    
    var fee = 0;
    if (feeType === 'fixed') {
        fee = feeFixed;
    } else if (feeType === 'percentage') {
        fee = amount * (feePercentage / 100);
    } else if (feeType === 'both') {
        fee = feeFixed + (amount * (feePercentage / 100));
    }
    
    var netAmount = amount - fee;
    
    $('#calculated-fee').text('€' + fee.toFixed(2));
    $('#net-amount').text('€' + netAmount.toFixed(2));
}

$('#fee-amount').on('input', calculateFee);
calculateFee(); // Initial calculation
");
?>
