<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $kpis array */
/* @var $revenueData array */
/* @var $orderData array */
/* @var $paymentData array */
/* @var $productData array */
/* @var $userData array */
/* @var $contentData array */
/* @var $dateFrom string */
/* @var $dateTo string */

$this->title = 'Analytics Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js');
?>

<div class="analytics-dashboard">
    
    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-calendar"></i> Analytics Period
                    </h5>
                </div>
                <div class="card-body">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'method' => 'get',
                        'options' => ['class' => 'form-inline'],
                    ]); ?>
                    
                    <div class="form-group mr-3">
                        <label for="date_from" class="mr-2">From:</label>
                        <?= Html::input('date', 'date_from', $dateFrom, ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group mr-3">
                        <label for="date_to" class="mr-2">To:</label>
                        <?= Html::input('date', 'date_to', $dateTo, ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="form-group mr-3">
                        <?= Html::submitButton('<i class="fas fa-search"></i> Update', ['class' => 'btn btn-primary']) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= Html::a('<i class="fas fa-download"></i> Export Report', ['export', 'date_from' => $dateFrom, 'date_to' => $dateTo], ['class' => 'btn btn-success']) ?>
                    </div>
                    
                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= Yii::$app->formatter->asCurrency($kpis['revenue']['current'], 'EUR') ?></h4>
                            <p class="mb-0">Total Revenue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-euro-sign fa-2x"></i>
                        </div>
                    </div>
                    <?php if ($kpis['revenue']['change'] != 0): ?>
                        <div class="mt-2">
                            <small>
                                <?php if ($kpis['revenue']['change'] > 0): ?>
                                    <i class="fas fa-arrow-up"></i> +<?= number_format($kpis['revenue']['change'], 1) ?>%
                                <?php else: ?>
                                    <i class="fas fa-arrow-down"></i> <?= number_format($kpis['revenue']['change'], 1) ?>%
                                <?php endif; ?>
                                vs previous period
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($kpis['orders']['current']) ?></h4>
                            <p class="mb-0">Total Orders</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                    <?php if ($kpis['orders']['change'] != 0): ?>
                        <div class="mt-2">
                            <small>
                                <?php if ($kpis['orders']['change'] > 0): ?>
                                    <i class="fas fa-arrow-up"></i> +<?= number_format($kpis['orders']['change'], 1) ?>%
                                <?php else: ?>
                                    <i class="fas fa-arrow-down"></i> <?= number_format($kpis['orders']['change'], 1) ?>%
                                <?php endif; ?>
                                vs previous period
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= Yii::$app->formatter->asCurrency($kpis['avgOrderValue']['current'], 'EUR') ?></h4>
                            <p class="mb-0">Avg Order Value</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($userData['new']) ?></h4>
                            <p class="mb-0">New Customers</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue and Orders Chart -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-area"></i> Revenue & Orders Trend
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie"></i> Payment Methods
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-credit-card"></i> Payment Method Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th class="text-right">Transactions</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Fees</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paymentData as $data): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-info"><?= Html::encode($data['method']->getTypeLabel()) ?></span>
                                            <?= Html::encode($data['method']->name) ?>
                                        </td>
                                        <td class="text-right"><?= number_format($data['transactions']) ?></td>
                                        <td class="text-right"><?= Yii::$app->formatter->asCurrency($data['amount'], 'EUR') ?></td>
                                        <td class="text-right"><?= Yii::$app->formatter->asCurrency($data['fees'], 'EUR') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-tasks"></i> Order Status Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <h3 class="text-success"><?= number_format($orderData['completed']) ?></h3>
                                <p class="mb-0">Completed Orders</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center mb-3">
                                <h3 class="text-warning"><?= number_format($orderData['pending']) ?></h3>
                                <p class="mb-0">Pending Orders</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?= $orderData['completionRate'] ?>%" 
                             aria-valuenow="<?= $orderData['completionRate'] ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                            <?= number_format($orderData['completionRate'], 1) ?>% Completion Rate
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <small class="text-muted">Products</small>
                            <h5><?= number_format($productData['total']) ?></h5>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Blog Posts</small>
                            <h5><?= number_format($contentData['totalPosts']) ?></h5>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Total Users</small>
                            <h5><?= number_format($userData['total']) ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-bolt"></i> Quick Analytics Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <?= Html::a('<i class="fas fa-chart-line"></i> Sales Analytics', ['sales'], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('<i class="fas fa-credit-card"></i> Payment Analytics', ['payments'], ['class' => 'btn btn-success']) ?>
                        <?= Html::a('<i class="fas fa-users"></i> User Analytics', ['users'], ['class' => 'btn btn-info']) ?>
                        <?= Html::a('<i class="fas fa-cog"></i> Generate Report', ['generate-report'], ['class' => 'btn btn-warning']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
// Prepare data for charts
$revenueLabels = json_encode(array_column($revenueData['daily'], 'date'));
$revenueValues = json_encode(array_column($revenueData['daily'], 'revenue'));

$paymentMethodLabels = [];
$paymentMethodValues = [];
foreach ($paymentData as $item) {
    $paymentMethodLabels[] = $item['method']->name;
    $paymentMethodValues[] = $item['amount'];
}
$paymentMethodLabels = json_encode($paymentMethodLabels);
$paymentMethodValues = json_encode($paymentMethodValues);

$this->registerJs("
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: $revenueLabels,
        datasets: [{
            label: 'Revenue (€)',
            data: $revenueValues,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
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
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: €' + context.parsed.y.toFixed(2);
                    }
                }
            }
        }
    }
});

// Payment Methods Chart
const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
const paymentChart = new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: $paymentMethodLabels,
        datasets: [{
            data: $paymentMethodValues,
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
                position: 'bottom'
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
");
?>
