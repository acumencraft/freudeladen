<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ShippingMethod;

/* @var $this yii\web\View */
/* @var $model common\models\ShippingMethod */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Shipping Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shipping-method-view">

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
                    <div class="card-tools">
                        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this shipping method?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </div>
                </div>
                <div class="card-body">
                    
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'name',
                            'code',
                            [
                                'attribute' => 'description',
                                'format' => 'ntext',
                            ],
                            [
                                'attribute' => 'provider',
                                'value' => function ($model) {
                                    return $model->getProviderLabel();
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->status == ShippingMethod::STATUS_ACTIVE) {
                                        return '<span class="badge badge-success">Active</span>';
                                    } else {
                                        return '<span class="badge badge-danger">Inactive</span>';
                                    }
                                },
                            ],
                            'sort_order',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                    
                </div>
            </div>
            
            <!-- Settings Card -->
            <?php if ($model->settings): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Provider Settings</h3>
                </div>
                <div class="card-body">
                    <?php $settings = $model->getSettingsArray(); ?>
                    
                    <div class="row">
                        <?php foreach ($settings as $key => $value): ?>
                        <div class="col-md-6 mb-3">
                            <strong><?= Html::encode(ucwords(str_replace('_', ' ', $key))) ?>:</strong>
                            <?php if (is_bool($value)): ?>
                                <span class="badge badge-<?= $value ? 'success' : 'secondary' ?>">
                                    <?= $value ? 'Yes' : 'No' ?>
                                </span>
                            <?php elseif (stripos($key, 'secret') !== false || stripos($key, 'key') !== false): ?>
                                <span class="text-muted">[Protected]</span>
                            <?php else: ?>
                                <?= Html::encode($value) ?>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <div class="col-md-4">
            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-shipping-fast"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Shipping Rates</span>
                            <span class="info-box-number"><?= $model->getRatesCount() ?></span>
                        </div>
                    </div>
                    
                    <?php if ($model->getRatesCount() > 0): ?>
                    <div class="mt-3">
                        <?= Html::a('Manage Rates', ['/shipping-rate/index', 'method_id' => $model->id], [
                            'class' => 'btn btn-info btn-block'
                        ]) ?>
                        <?= Html::a('Add New Rate', ['/shipping-rate/create', 'method_id' => $model->id], [
                            'class' => 'btn btn-success btn-block mt-2'
                        ]) ?>
                    </div>
                    <?php else: ?>
                    <div class="mt-3">
                        <?= Html::a('Add First Rate', ['/shipping-rate/create', 'method_id' => $model->id], [
                            'class' => 'btn btn-success btn-block'
                        ]) ?>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    
                    <?= Html::a(
                        $model->status == ShippingMethod::STATUS_ACTIVE 
                            ? '<i class="fas fa-pause"></i> Deactivate' 
                            : '<i class="fas fa-play"></i> Activate',
                        ['toggle-status', 'id' => $model->id],
                        [
                            'class' => 'btn btn-' . ($model->status == ShippingMethod::STATUS_ACTIVE ? 'warning' : 'success') . ' btn-block',
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure you want to change the status?'
                        ]
                    ) ?>
                    
                    <?= Html::a('<i class="fas fa-copy"></i> Duplicate', ['duplicate', 'id' => $model->id], [
                        'class' => 'btn btn-info btn-block mt-2',
                        'data-method' => 'post'
                    ]) ?>
                    
                    <?= Html::a('<i class="fas fa-list"></i> Back to List', ['index'], [
                        'class' => 'btn btn-secondary btn-block mt-2'
                    ]) ?>
                    
                </div>
            </div>
            
        </div>
    </div>

</div>
