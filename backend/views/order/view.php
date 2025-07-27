<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Order $model */

$this->title = 'Bestellung ' . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Bestellungen', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$statusLabels = [
    'pending' => ['label' => 'Ausstehend', 'class' => 'warning'],
    'processing' => ['label' => 'In Bearbeitung', 'class' => 'info'],
    'shipped' => ['label' => 'Versandt', 'class' => 'primary'],
    'delivered' => ['label' => 'Geliefert', 'class' => 'success'],
    'cancelled' => ['label' => 'Storniert', 'class' => 'danger'],
];
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Bearbeiten', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Zurück zur Liste', ['index'], ['class' => 'btn btn-secondary']) ?>
        
        <!-- Status Change Buttons -->
        <?php if ($model->status == 'pending'): ?>
            <?= Html::a('In Bearbeitung', ['change-status', 'id' => $model->id, 'status' => 'processing'], [
                'class' => 'btn btn-info',
                'data' => ['confirm' => 'Status zu "In Bearbeitung" ändern?']
            ]) ?>
        <?php endif; ?>
        
        <?php if ($model->status == 'processing'): ?>
            <?= Html::a('Als versandt markieren', ['change-status', 'id' => $model->id, 'status' => 'shipped'], [
                'class' => 'btn btn-primary',
                'data' => ['confirm' => 'Status zu "Versandt" ändern?']
            ]) ?>
        <?php endif; ?>
        
        <?php if ($model->status == 'shipped'): ?>
            <?= Html::a('Als geliefert markieren', ['change-status', 'id' => $model->id, 'status' => 'delivered'], [
                'class' => 'btn btn-success',
                'data' => ['confirm' => 'Status zu "Geliefert" ändern?']
            ]) ?>
        <?php endif; ?>
        
        <?php if (!in_array($model->status, ['cancelled', 'delivered'])): ?>
            <?= Html::a('Stornieren', ['change-status', 'id' => $model->id, 'status' => 'cancelled'], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Bestellung wirklich stornieren?']
            ]) ?>
        <?php endif; ?>
    </p>

    <div class="row">
        <div class="col-md-8">
            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Bestelldetails</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'order_number',
                            [
                                'attribute' => 'status',
                                'value' => function($model) use ($statusLabels) {
                                    $status = $statusLabels[$model->status] ?? ['label' => $model->status, 'class' => 'secondary'];
                                    return '<span class="badge bg-' . $status['class'] . '">' . $status['label'] . '</span>';
                                },
                                'format' => 'raw',
                                'label' => 'Status'
                            ],
                            [
                                'attribute' => 'total_amount',
                                'value' => '€' . number_format($model->total_amount, 2),
                                'label' => 'Gesamtbetrag'
                            ],
                            [
                                'attribute' => 'created_at',
                                'format' => 'datetime',
                                'label' => 'Bestellt am'
                            ],
                            [
                                'attribute' => 'updated_at',
                                'format' => 'datetime',
                                'label' => 'Zuletzt aktualisiert'
                            ],
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Kundeninformationen</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'customer_name',
                            'customer_email:email',
                            'customer_phone',
                            'billing_address:ntext',
                            'shipping_address:ntext',
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h5>Bestellpositionen</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($model->orderItems)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produkt</th>
                                        <th>Menge</th>
                                        <th>Einzelpreis</th>
                                        <th>Gesamt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($model->orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?= Html::encode($item->product_name) ?></strong>
                                                <?php if ($item->product): ?>
                                                    <br><small class="text-muted">Artikel-ID: <?= Html::encode($item->product->id) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $item->quantity ?></td>
                                            <td>€<?= number_format($item->price, 2) ?></td>
                                            <td><strong>€<?= number_format($item->quantity * $item->price, 2) ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Gesamtsumme:</th>
                                        <th>€<?= number_format($model->total_amount, 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Keine Bestellpositionen gefunden.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="card">
                <div class="card-header">
                    <h5>Bestellübersicht</h5>
                </div>
                <div class="card-body">
                    <p><strong>Anzahl Artikel:</strong> <?= count($model->orderItems) ?></p>
                    <p><strong>Gesamtmenge:</strong> <?= array_sum(array_column($model->orderItems, 'quantity')) ?></p>
                    <p><strong>Bestellwert:</strong> €<?= number_format($model->total_amount, 2) ?></p>
                    <hr>
                    <p><strong>Zahlungsmethode:</strong> <?= Html::encode($model->payment_method ?? 'Nicht angegeben') ?></p>
                    <p><strong>Zahlungsstatus:</strong> 
                        <span class="badge bg-<?= $model->payment_status == 'paid' ? 'success' : 'warning' ?>">
                            <?= $model->payment_status == 'paid' ? 'Bezahlt' : 'Ausstehend' ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
