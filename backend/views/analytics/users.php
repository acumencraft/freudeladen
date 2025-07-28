<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dateFrom string */
/* @var $dateTo string */
/* @var $userStats array */
/* @var $totalUsers int */
/* @var $newUsers int */
/* @var $activeUsers int */
/* @var $userGrowth float */
/* @var $dailyRegistrations array */
/* @var $userActivity array */
/* @var $topCustomers array */
/* @var $customerSegments array */

$this->title = 'User Analytics';
$this->params['breadcrumbs'][] = ['label' => 'Analytics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js');
?>

<div class="user-analytics">
    
    <!-- User Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($totalUsers) ?></h4>
                            <p class="mb-0">Total Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
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
                            <h4 class="mb-0"><?= number_format($newUsers) ?></h4>
                            <p class="mb-0">New Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-plus fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>
                            <?php if ($userGrowth > 0): ?>
                                <i class="fas fa-arrow-up"></i> +<?= number_format($userGrowth, 1) ?>%
                            <?php elseif ($userGrowth < 0): ?>
                                <i class="fas fa-arrow-down"></i> <?= number_format($userGrowth, 1) ?>%
                            <?php else: ?>
                                <i class="fas fa-minus"></i> 0%
                            <?php endif; ?>
                            vs previous period
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($activeUsers) ?></h4>
                            <p class="mb-0">Active Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>
                            <?= $totalUsers > 0 ? number_format(($activeUsers / $totalUsers) * 100, 1) : 0 ?>% of total users
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($userStats['avgSessionTime']) ?>m</h4>
                            <p class="mb-0">Avg Session</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Registration Trends -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line"></i> User Registration Trends
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="registrationChart" height="120"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie"></i> User Segments
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="segmentChart" height="150"></canvas>
                    
                    <div class="mt-3">
                        <?php 
                        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];
                        $index = 0;
                        foreach ($customerSegments as $segment => $count): 
                        ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fas fa-circle" style="color: <?= $colors[$index % 5] ?>"></i>
                                    <?= Html::encode($segment) ?>
                                </span>
                                <span class="badge badge-primary"><?= number_format($count) ?></span>
                            </div>
                        <?php 
                        $index++;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers and Activity -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-star"></i> Top Customers by Value
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th class="text-right">Orders</th>
                                    <th class="text-right">Total Spent</th>
                                    <th class="text-right">Avg Order</th>
                                    <th>Registration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topCustomers as $index => $customer): ?>
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
                                            <strong><?= Html::encode($customer['username']) ?></strong>
                                            <br><small class="text-muted"><?= Html::encode($customer['email']) ?></small>
                                        </td>
                                        <td class="text-right">
                                            <span class="badge badge-primary"><?= number_format($customer['order_count']) ?></span>
                                        </td>
                                        <td class="text-right">
                                            <strong><?= Yii::$app->formatter->asCurrency($customer['total_spent'], 'EUR') ?></strong>
                                        </td>
                                        <td class="text-right">
                                            <?= Yii::$app->formatter->asCurrency($customer['avg_order'], 'EUR') ?>
                                        </td>
                                        <td>
                                            <?= Yii::$app->formatter->asDate($customer['created_at']) ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $customer['status'] == 10 ? 'success' : 'secondary' ?>">
                                                <?= $customer['status'] == 10 ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-bar"></i> User Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Login Activity (Last 7 Days)</h6>
                        <canvas id="activityChart" height="150"></canvas>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-md-6">
                            <h5 class="text-primary"><?= number_format($userActivity['daily_logins']) ?></h5>
                            <small class="text-muted">Daily Logins</small>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-info"><?= number_format($userActivity['page_views']) ?></h5>
                            <small class="text-muted">Page Views</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-md-6">
                            <h6 class="text-success"><?= number_format($userActivity['returning_users']) ?></h6>
                            <small class="text-muted">Returning Users</small>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning"><?= number_format($userActivity['bounce_rate'], 1) ?>%</h6>
                            <small class="text-muted">Bounce Rate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Demographics and Statistics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-users-cog"></i> User Demographics & Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h4 class="text-primary"><?= number_format($userStats['admin_users']) ?></h4>
                            <p class="mb-0">Admin Users</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-success"><?= number_format($userStats['verified_users']) ?></h4>
                            <p class="mb-0">Verified Users</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-info"><?= number_format($userStats['users_with_orders']) ?></h4>
                            <p class="mb-0">Users with Orders</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class="text-warning"><?= number_format($userStats['inactive_users']) ?></h4>
                            <p class="mb-0">Inactive Users</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>User Engagement Metrics</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= $totalUsers > 0 ? ($userStats['users_with_orders'] / $totalUsers) * 100 : 0 ?>%" 
                                     aria-valuenow="<?= $totalUsers > 0 ? ($userStats['users_with_orders'] / $totalUsers) * 100 : 0 ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?= $totalUsers > 0 ? number_format(($userStats['users_with_orders'] / $totalUsers) * 100, 1) : 0 ?>% Purchase Rate
                                </div>
                            </div>
                            
                            <div class="progress mb-2">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: <?= $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0 ?>%" 
                                     aria-valuenow="<?= $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0 ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?= $totalUsers > 0 ? number_format(($activeUsers / $totalUsers) * 100, 1) : 0 ?>% Active Rate
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Registration Trends</h6>
                            <p class="mb-1">
                                <strong>This Month:</strong> <?= number_format($userStats['monthly_registrations']) ?> new users
                            </p>
                            <p class="mb-1">
                                <strong>This Week:</strong> <?= number_format($userStats['weekly_registrations']) ?> new users
                            </p>
                            <p class="mb-1">
                                <strong>Average per Day:</strong> <?= number_format($userStats['avg_daily_registrations'], 1) ?> new users
                            </p>
                        </div>
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
                <?= Html::a('<i class="fas fa-download"></i> Export User Report', ['export-users', 'date_from' => $dateFrom, 'date_to' => $dateTo], ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-users"></i> Manage Users', ['/user'], ['class' => 'btn btn-info']) ?>
                <?= Html::a('<i class="fas fa-envelope"></i> Email Campaign', ['#'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

</div>

<?php
// Prepare data for charts
$registrationLabels = json_encode(array_column($dailyRegistrations, 'date'));
$registrationValues = json_encode(array_column($dailyRegistrations, 'registrations'));

$segmentLabels = json_encode(array_keys($customerSegments));
$segmentValues = json_encode(array_values($customerSegments));

// Activity data (last 7 days)
$activityLabels = json_encode(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
$activityValues = json_encode([120, 140, 110, 160, 180, 200, 150]); // Sample data

$this->registerJs("
// Registration Chart
const registrationCtx = document.getElementById('registrationChart').getContext('2d');
const registrationChart = new Chart(registrationCtx, {
    type: 'line',
    data: {
        labels: $registrationLabels,
        datasets: [{
            label: 'New Registrations',
            data: $registrationValues,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
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
                    stepSize: 1
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Registrations: ' + context.parsed.y;
                    }
                }
            }
        }
    }
});

// Segment Chart
const segmentCtx = document.getElementById('segmentChart').getContext('2d');
const segmentChart = new Chart(segmentCtx, {
    type: 'doughnut',
    data: {
        labels: $segmentLabels,
        datasets: [{
            data: $segmentValues,
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
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Activity Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(activityCtx, {
    type: 'bar',
    data: {
        labels: $activityLabels,
        datasets: [{
            label: 'Daily Logins',
            data: $activityValues,
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
");
?>
