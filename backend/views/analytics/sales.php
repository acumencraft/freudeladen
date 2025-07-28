<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $dateFrom string */
/* @var $dateTo string */
/* @var $salesData array */
/* @var $totalRevenue float */
/* @var $totalOrders int */
/* @var $avgOrderValue float */
/* @var $topProducts array */
/* @var $topCategories array */
/* @var $dailyStats array */

$this->title = 'Sales Analytics';
$this->params['breadcrumbs'][] = ['label' => 'Analytics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js');
?>

<div class="sales-analytics">
    
    <!-- Header with Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h3 class="mb-0"><?= Yii::$app->formatter->asCurrency($totalRevenue, 'EUR') ?></h3>
                            <small>Total Revenue</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="mb-0"><?= number_format($totalOrders) ?></h3>
                            <small>Total Orders</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="mb-0"><?= Yii::$app->formatter->asCurrency($avgOrderValue, 'EUR') ?></h3>
                            <small>Avg Order Value</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="mb-0"><?= date('M j', strtotime($dateFrom)) ?> - <?= date('M j', strtotime($dateTo)) ?></h3>
                            <small>Analysis Period</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Sales Chart -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Daily Sales Performance
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active" onclick="switchChart('revenue')">Revenue</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="switchChart('orders')">Orders</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="switchChart('both')">Both</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products and Categories -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-trophy"></i> Top Selling Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-right">Qty Sold</th>
                                    <th class="text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topProducts as $index => $product): ?>
                                    <tr>
                                        <td>
                                            <?php if ($index < 3): ?>
                                                <span class="badge badge-<?= $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'info') ?>">
                                                    #<?= $index + 1 ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">#<?= $index + 1 ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= Html::encode($product['name']) ?></strong>
                                            <br><small class="text-muted">SKU: <?= Html::encode($product['sku']) ?></small>
                                        </td>
                                        <td class="text-right">
                                            <span class="badge badge-success"><?= number_format($product['quantity']) ?></span>
                                        </td>
                                        <td class="text-right">
                                            <strong><?= Yii::$app->formatter->asCurrency($product['revenue'], 'EUR') ?></strong>
                                        </td>
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
                        <i class="fas fa-tags"></i> Top Categories
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                    
                    <div class="mt-3">
                        <?php foreach ($topCategories as $index => $category): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fas fa-circle" style="color: <?= ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'][$index % 5] ?>"></i>
                                    <?= Html::encode($category['name']) ?>
                                </span>
                                <span class="badge badge-primary">
                                    <?= Yii::$app->formatter->asCurrency($category['revenue'], 'EUR') ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Trends Table -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-table"></i> Daily Sales Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Revenue</th>
                                    <th class="text-right">Avg Order</th>
                                    <th class="text-right">Items Sold</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $previousRevenue = null;
                                foreach ($dailyStats as $day): 
                                    $trend = null;
                                    if ($previousRevenue !== null) {
                                        if ($day['revenue'] > $previousRevenue) {
                                            $trend = '<i class="fas fa-arrow-up text-success"></i>';
                                        } elseif ($day['revenue'] < $previousRevenue) {
                                            $trend = '<i class="fas fa-arrow-down text-danger"></i>';
                                        } else {
                                            $trend = '<i class="fas fa-minus text-muted"></i>';
                                        }
                                    }
                                    $previousRevenue = $day['revenue'];
                                ?>
                                    <tr>
                                        <td><?= Yii::$app->formatter->asDate($day['date']) ?></td>
                                        <td class="text-right"><?= number_format($day['orders']) ?></td>
                                        <td class="text-right"><?= Yii::$app->formatter->asCurrency($day['revenue'], 'EUR') ?></td>
                                        <td class="text-right"><?= $day['orders'] > 0 ? Yii::$app->formatter->asCurrency($day['revenue'] / $day['orders'], 'EUR') : '-' ?></td>
                                        <td class="text-right"><?= number_format($day['items']) ?></td>
                                        <td><?= $trend ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
                <?= Html::a('<i class="fas fa-download"></i> Export Sales Report', ['export-sales', 'date_from' => $dateFrom, 'date_to' => $dateTo], ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-print"></i> Print Report', '#', ['class' => 'btn btn-info', 'onclick' => 'window.print(); return false;']) ?>
            </div>
        </div>
    </div>

</div>

<?php
// Prepare data for charts
$salesLabels = json_encode(array_column($dailyStats, 'date'));
$salesRevenue = json_encode(array_column($dailyStats, 'revenue'));
$salesOrders = json_encode(array_column($dailyStats, 'orders'));

$categoryLabels = [];
$categoryValues = [];
foreach ($topCategories as $category) {
    $categoryLabels[] = $category['name'];
    $categoryValues[] = $category['revenue'];
}
$categoryLabels = json_encode($categoryLabels);
$categoryValues = json_encode($categoryValues);

$this->registerJs("
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
let salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: $salesLabels,
        datasets: [{
            label: 'Revenue (€)',
            data: $salesRevenue,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
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
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': €' + context.parsed.y.toFixed(2);
                    }
                }
            }
        }
    }
});

// Function to switch chart view
function switchChart(type) {
    // Update button states
    document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    salesChart.destroy();
    
    let datasets = [];
    let scales = {
        y: {
            type: 'linear',
            display: true,
            position: 'left',
            beginAtZero: true
        }
    };
    
    if (type === 'revenue') {
        datasets = [{
            label: 'Revenue (€)',
            data: $salesRevenue,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }];
        scales.y.ticks = {
            callback: function(value) { return '€' + value.toFixed(2); }
        };
    } else if (type === 'orders') {
        datasets = [{
            label: 'Orders',
            data: $salesOrders,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }];
        scales.y.ticks = {
            callback: function(value) { return value; }
        };
    } else {
        datasets = [{
            label: 'Revenue (€)',
            data: $salesRevenue,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
        }, {
            label: 'Orders',
            data: $salesOrders,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            fill: false,
            tension: 0.4,
            yAxisID: 'y1'
        }];
        scales.y1 = {
            type: 'linear',
            display: true,
            position: 'right',
            beginAtZero: true,
            grid: {
                drawOnChartArea: false,
            },
        };
        scales.y.ticks = {
            callback: function(value) { return '€' + value.toFixed(2); }
        };
    }
    
    salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: $salesLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: scales,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label.includes('Revenue')) {
                                return context.dataset.label + ': €' + context.parsed.y.toFixed(2);
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
}

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: $categoryLabels,
        datasets: [{
            data: $categoryValues,
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': €' + context.parsed.toFixed(2) + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
");
?>
