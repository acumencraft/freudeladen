<?php

use yii\helpers\Html;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $dateFrom string */
/* @var $dateTo string */
/* @var $paymentMethodStats array */
/* @var $transactionStats array */
/* @var $totalTransactions int */
/* @var $totalAmount float */
/* @var $totalFees float */
/* @var $averageTransaction float */
/* @var $successRate float */
/* @var $dailyPayments array */
/* @var $providerStats array */

$this->title = 'Payment Analytics';
$this->params['breadcrumbs'][] = ['label' => 'Analytics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js');
?>

<div class="payment-analytics">
    
    <!-- Payment Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($totalTransactions) ?></h4>
                            <p class="mb-0">Total Transactions</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-credit-card fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= Yii::$app->formatter->asCurrency($totalAmount, 'EUR') ?></h4>
                            <p class="mb-0">Total Amount</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-euro-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= Yii::$app->formatter->asCurrency($totalFees, 'EUR') ?></h4>
                            <p class="mb-0">Total Fees</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($successRate, 1) ?>%</h4>
                            <p class="mb-0">Success Rate</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Trends Chart -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line"></i> Daily Payment Trends
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentTrendsChart" height="120"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie"></i> Payment Method Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Performance -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-table"></i> Payment Method Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Type</th>
                                    <th>Provider</th>
                                    <th class="text-right">Transactions</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Fees</th>
                                    <th class="text-right">Avg Transaction</th>
                                    <th class="text-right">Success Rate</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paymentMethodStats as $stats): ?>
                                    <tr>
                                        <td>
                                            <strong><?= Html::encode($stats['method']->name) ?></strong>
                                            <br><small class="text-muted"><?= Html::encode($stats['method']->description) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?= Html::encode($stats['method']->getTypeLabel()) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($stats['method']->provider): ?>
                                                <span class="badge badge-info">
                                                    <?= Html::encode($stats['method']->getProviderLabel()) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <span class="badge badge-primary"><?= number_format($stats['transactions']) ?></span>
                                        </td>
                                        <td class="text-right">
                                            <strong><?= Yii::$app->formatter->asCurrency($stats['amount'], 'EUR') ?></strong>
                                        </td>
                                        <td class="text-right">
                                            <?= Yii::$app->formatter->asCurrency($stats['fees'], 'EUR') ?>
                                        </td>
                                        <td class="text-right">
                                            <?= $stats['transactions'] > 0 ? Yii::$app->formatter->asCurrency($stats['amount'] / $stats['transactions'], 'EUR') : '-' ?>
                                        </td>
                                        <td class="text-right">
                                            <?php 
                                            $rate = $stats['transactions'] > 0 ? ($stats['successful'] / $stats['transactions']) * 100 : 0;
                                            $badgeClass = $rate >= 95 ? 'success' : ($rate >= 90 ? 'warning' : 'danger');
                                            ?>
                                            <span class="badge badge-<?= $badgeClass ?>"><?= number_format($rate, 1) ?>%</span>
                                        </td>
                                        <td>
                                            <?php if ($stats['method']->is_active): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Provider Performance and Transaction Status -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-building"></i> Provider Performance
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="providerChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-tasks"></i> Transaction Status Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h3 class="text-success"><?= number_format($transactionStats['completed']) ?></h3>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h3 class="text-warning"><?= number_format($transactionStats['pending']) ?></h3>
                                <p class="mb-0">Pending</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h3 class="text-danger"><?= number_format($transactionStats['failed']) ?></h3>
                                <p class="mb-0">Failed</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress mb-3">
                        <?php 
                        $total = $transactionStats['completed'] + $transactionStats['pending'] + $transactionStats['failed'];
                        $completedPercent = $total > 0 ? ($transactionStats['completed'] / $total) * 100 : 0;
                        $pendingPercent = $total > 0 ? ($transactionStats['pending'] / $total) * 100 : 0;
                        $failedPercent = $total > 0 ? ($transactionStats['failed'] / $total) * 100 : 0;
                        ?>
                        <div class="progress-bar bg-success" style="width: <?= $completedPercent ?>%"></div>
                        <div class="progress-bar bg-warning" style="width: <?= $pendingPercent ?>%"></div>
                        <div class="progress-bar bg-danger" style="width: <?= $failedPercent ?>%"></div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <small class="text-muted">Completed</small>
                            <h6><?= number_format($completedPercent, 1) ?>%</h6>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Pending</small>
                            <h6><?= number_format($pendingPercent, 1) ?>%</h6>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Failed</small>
                            <h6><?= number_format($failedPercent, 1) ?>%</h6>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <h5>Average Transaction: <?= Yii::$app->formatter->asCurrency($averageTransaction, 'EUR') ?></h5>
                        <p class="text-muted">Based on completed transactions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group" role="group">
                <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Dashboard', ['index'], ['class' => 'btn btn-secondary']) ?>
                <?= Html::a('<i class="fas fa-download"></i> Export Payment Report', ['export-payments', 'date_from' => $dateFrom, 'date_to' => $dateTo], ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-cog"></i> Payment Methods', ['/payment-method'], ['class' => 'btn btn-info']) ?>
                <?= Html::a('<i class="fas fa-list"></i> All Transactions', ['/transaction'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

</div>

<?php
// Prepare data for charts
$trendsLabels = json_encode(array_column($dailyPayments, 'date'));
$trendsAmounts = json_encode(array_column($dailyPayments, 'amount'));
$trendsTransactions = json_encode(array_column($dailyPayments, 'transactions'));

$methodLabels = [];
$methodValues = [];
foreach ($paymentMethodStats as $stats) {
    $methodLabels[] = $stats['method']->name;
    $methodValues[] = $stats['amount'];
}
$methodLabels = json_encode($methodLabels);
$methodValues = json_encode($methodValues);

$providerLabels = [];
$providerValues = [];
foreach ($providerStats as $provider => $amount) {
    $providerLabels[] = $provider ?: 'Other';
    $providerValues[] = $amount;
}
$providerLabels = json_encode($providerLabels);
$providerValues = json_encode($providerValues);

$this->registerJs("
// Payment Trends Chart
const trendsCtx = document.getElementById('paymentTrendsChart').getContext('2d');
const trendsChart = new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: $trendsLabels,
        datasets: [{
            label: 'Amount (€)',
            data: $trendsAmounts,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
        }, {
            label: 'Transactions',
            data: $trendsTransactions,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            fill: false,
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '€' + value.toFixed(2);
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false,
                },
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        if (context.dataset.label.includes('Amount')) {
                            return context.dataset.label + ': €' + context.parsed.y.toFixed(2);
                        }
                        return context.dataset.label + ': ' + context.parsed.y;
                    }
                }
            }
        }
    }
});

// Payment Methods Chart
const methodCtx = document.getElementById('paymentMethodChart').getContext('2d');
const methodChart = new Chart(methodCtx, {
    type: 'doughnut',
    data: {
        labels: $methodLabels,
        datasets: [{
            data: $methodValues,
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40',
                '#FF6384',
                '#C9CBCF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    fontSize: 10
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': €' + context.parsed.toFixed(2);
                    }
                }
            }
        }
    }
});

// Provider Chart
const providerCtx = document.getElementById('providerChart').getContext('2d');
const providerChart = new Chart(providerCtx, {
    type: 'bar',
    data: {
        labels: $providerLabels,
        datasets: [{
            label: 'Transaction Volume (€)',
            data: $providerValues,
            backgroundColor: [
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '€' + value.toFixed(2);
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': €' + context.parsed.y.toFixed(2);
                    }
                }
            }
        }
    }
});
");
?>
