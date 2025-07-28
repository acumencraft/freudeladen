<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

$this->title = 'Benutzer: ' . $model->email;
$this->params['breadcrumbs'][] = ['label' => 'Benutzer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="fas fa-edit me-1"></i>Bearbeiten', ['update', 'id' => $model->id], [
                'class' => 'btn btn-primary'
            ]) ?>
            <?php if ($model->status): ?>
                <?= Html::a('<i class="fas fa-ban me-1"></i>Blockieren', ['block', 'id' => $model->id], [
                    'class' => 'btn btn-outline-danger',
                    'data-confirm' => 'Sind Sie sicher, dass Sie diesen Benutzer blockieren möchten?',
                    'data-method' => 'post',
                ]) ?>
            <?php else: ?>
                <?= Html::a('<i class="fas fa-check me-1"></i>Entsperren', ['unblock', 'id' => $model->id], [
                    'class' => 'btn btn-outline-success',
                    'data-confirm' => 'Sind Sie sicher, dass Sie diesen Benutzer entsperren möchten?',
                    'data-method' => 'post',
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Benutzerinformationen</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped table-bordered detail-view'],
                        'attributes' => [
                            'id',
                            'email:email',
                            'phone',
                            [
                                'attribute' => 'email_verified',
                                'format' => 'html',
                                'value' => $model->email_verified ? 
                                    '<span class="badge bg-success">Verifiziert</span>' : 
                                    '<span class="badge bg-warning">Nicht verifiziert</span>',
                            ],
                            [
                                'attribute' => 'phone_verified',
                                'format' => 'html',
                                'value' => $model->phone_verified ? 
                                    '<span class="badge bg-success">Verifiziert</span>' : 
                                    '<span class="badge bg-warning">Nicht verifiziert</span>',
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => $model->status ? 
                                    '<span class="badge bg-success">Aktiv</span>' : 
                                    '<span class="badge bg-danger">Blockiert</span>',
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
                    <h5 class="card-title mb-0">Adressen</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($addresses)): ?>
                        <?php foreach ($addresses as $address): ?>
                            <div class="mb-3 p-3 border rounded">
                                <h6 class="fw-bold">
                                    <?= Html::encode($address->getTypeOptions()[$address->type]) ?>
                                    <?php if ($address->is_default): ?>
                                        <span class="badge bg-primary">Standard</span>
                                    <?php endif; ?>
                                </h6>
                                <p class="mb-0"><?= Html::encode($address->getFullAddress()) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Keine Adressen hinterlegt.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Bestellhistorie</h5>
                </div>
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider' => $ordersProvider,
                        'tableOptions' => ['class' => 'table table-striped table-hover'],
                        'columns' => [
                            'id',
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function ($model) {
                                    $statusLabels = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'refunded' => 'secondary',
                                    ];
                                    $label = $statusLabels[$model->status] ?? 'secondary';
                                    return '<span class="badge bg-' . $label . '">' . ucfirst($model->status) . '</span>';
                                },
                            ],
                            [
                                'attribute' => 'total',
                                'format' => 'currency',
                                'contentOptions' => ['class' => 'text-end'],
                            ],
                            'payment_method',
                            'created_at:datetime',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'controller' => 'order',
                                'template' => '{view}',
                                'headerOptions' => ['style' => 'width: 50px;'],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
