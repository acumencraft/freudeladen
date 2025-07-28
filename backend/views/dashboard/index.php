<?php

/* @var $this yii\web\View */
/* @var $stats array */
/* @var $recentOrders array */
/* @var $recentLogs array */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard - FREUDELADEN.DE Admin';
?>

<div class="dashboard-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
        <span class="text-muted">Willkommen zurück, <?= Yii::$app->user->identity->getDisplayName() ?>!</span>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Bestellungen (Heute)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['todayOrders']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Umsatz (Monat)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                €<?= number_format($stats['monthlyRevenue'], 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Produkte
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['totalProducts']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Wenig Lagerbestand
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats['lowStockProducts']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders and Activity -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Aktuelle Bestellungen
                    </h6>
                    <a href="<?= Url::to(['order/index']) ?>" class="btn btn-primary btn-sm">
                        Alle anzeigen
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentOrders)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>Keine aktuellen Bestellungen gefunden.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kunde</th>
                                        <th>Betrag</th>
                                        <th>Status</th>
                                        <th>Datum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?= $order->id ?></td>
                                            <td><?= Html::encode($order->first_name . ' ' . $order->last_name) ?></td>
                                            <td>€<?= number_format($order->total, 2) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'secondary') ?>">
                                                    <?= ucfirst($order->status) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d.m.Y H:i', strtotime($order->created_at)) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Letzte Aktivitäten
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($recentLogs)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-history fa-2x mb-3"></i>
                            <p>Keine Aktivitäten gefunden.</p>
                        </div>
                    <?php else: ?>
                        <div class="activity-log">
                            <?php foreach ($recentLogs as $log): ?>
                                <div class="activity-item d-flex align-items-start mb-3">
                                    <div class="activity-icon me-3">
                                        <i class="fas fa-<?= $log->action === 'login' ? 'sign-in-alt' : ($log->action === 'create' ? 'plus' : 'edit') ?> text-muted"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-description">
                                            <strong><?= $log->getUserDisplayName() ?></strong>
                                            <?= $log->getFormattedAction() ?>
                                            <?php if ($log->getObjectDescription()): ?>
                                                <em><?= $log->getObjectDescription() ?></em>
                                            <?php endif; ?>
                                        </div>
                                        <div class="activity-time text-muted small">
                                            <?= date('d.m.Y H:i', strtotime($log->created_at)) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Schnellzugriff
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= Url::to(['product/create']) ?>" class="btn btn-primary btn-block w-100">
                                <i class="fas fa-plus me-2"></i>Neues Produkt
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Url::to(['category/create']) ?>" class="btn btn-success btn-block w-100">
                                <i class="fas fa-tags me-2"></i>Neue Kategorie
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Url::to(['order/index']) ?>" class="btn btn-info btn-block w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Bestellungen verwalten
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-warning btn-block w-100">
                                <i class="fas fa-cog me-2"></i>Einstellungen
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    
    .text-xs {
        font-size: 0.7rem;
    }
    
    .activity-item {
        border-left: 2px solid #e3e6f0;
        padding-left: 1rem;
        position: relative;
    }
    
    .activity-item:last-child {
        border-left: none;
    }
    
    .activity-icon {
        background: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e3e6f0;
        margin-left: -16px;
    }
</style>
