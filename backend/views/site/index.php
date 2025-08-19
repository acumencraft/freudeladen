<?php

/** @var yii\web\View $this */
/** @var array $stats */

use yii\helpers\Html;

$this->title = 'Dashboard - FREUDELADEN.DE Admin';
?>
<div class="site-index">
    
    <div class="mb-4">
        <h1>Dashboard</h1>
        <p class="lead">Willkommen im FREUDELADEN.DE Administrationspanel</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= $stats['totalProducts'] ?></h4>
                            <p class="card-text">Produkte</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?= Html::a('Alle ansehen', ['/product/index'], ['class' => 'text-white text-decoration-none']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= $stats['totalOrders'] ?></h4>
                            <p class="card-text">Bestellungen</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?= Html::a('Alle ansehen', ['/order/index'], ['class' => 'text-white text-decoration-none']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= $stats['totalCategories'] ?></h4>
                            <p class="card-text">Kategorien</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?= Html::a('Alle ansehen', ['/category/index'], ['class' => 'text-white text-decoration-none']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?= $stats['pendingOrders'] ?></h4>
                            <p class="card-text">Offene Bestellungen</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?= Html::a('Bearbeiten', ['/order/index'], ['class' => 'text-white text-decoration-none']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Neueste Bestellungen</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($stats['recentOrders'])): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Bestell-Nr.</th>
                                        <th>Kunde</th>
                                        <th>Gesamt</th>
                                        <th>Status</th>
                                        <th>Datum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['recentOrders'] as $order): ?>
                                        <tr>
                                            <td><?= Html::a($order->order_number, ['/order/view', 'id' => $order->id], ['class' => 'text-decoration-none']) ?></td>
                                            <td><?= Html::encode($order->customer_name) ?></td>
                                            <td>€<?= number_format($order->total_amount, 2) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $order->status == 'pending' ? 'warning' : ($order->status == 'completed' ? 'success' : 'info') ?>">
                                                    <?= Html::encode(ucfirst($order->status)) ?>
                                                </span>
                                            </td>
                                            <td><?= Yii::$app->formatter->asDatetime($order->created_at) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Keine Bestellungen vorhanden.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Niedrige Lagerbestände</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($stats['lowStockProducts'])): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($stats['lowStockProducts'] as $product): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <strong><?= Html::a(Html::encode($product->name), ['/product/view', 'id' => $product->id], ['class' => 'text-decoration-none']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= Html::encode($product->category->name ?? 'Keine Kategorie') ?></small>
                                    </div>
                                    <span class="badge bg-<?= $product->stock == 0 ? 'danger' : ($product->stock < 5 ? 'warning' : 'info') ?> rounded-pill">
                                        <?= $product->stock ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Alle Produkte haben ausreichend Lagerbestand.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
</div>
