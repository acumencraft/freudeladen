<?php

/* @var $this yii\web\View */
/* @var $statistics array */

use yii\helpers\Html;

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashboard-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <h3>Total Products</h3>
                <div class="number"><?= $statistics['total_products'] ?? 0 ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h3>Total Orders</h3>
                <div class="number"><?= $statistics['total_orders'] ?? 0 ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h3>Total Users</h3>
                <div class="number"><?= $statistics['total_users'] ?? 0 ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h3>Blog Posts</h3>
                <div class="number"><?= $statistics['total_blog_posts'] ?? 0 ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Recent Orders</h3>
                </div>
                <div class="panel-body">
                    <?php if (!empty($statistics['recent_orders'])): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($statistics['recent_orders'] as $order): ?>
                                        <tr>
                                            <td><?= Html::encode($order->id) ?></td>
                                            <td><?= Html::encode($order->customer_name ?? 'N/A') ?></td>
                                            <td>$<?= number_format($order->total ?? 0, 2) ?></td>
                                            <td>
                                                <span class="label label-<?= $order->status === 'completed' ? 'success' : 'warning' ?>">
                                                    <?= Html::encode($order->status ?? 'pending') ?>
                                                </span>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($order->created_at ?? 'now')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No recent orders found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Quick Actions</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/product/create'])) ?>" class="btn btn-primary btn-lg">
                                <i class="glyphicon glyphicon-plus"></i><br>
                                Add Product
                            </a>
                        </div>
                        <div class="col-md-6 text-center">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/order/index'])) ?>" class="btn btn-info btn-lg">
                                <i class="glyphicon glyphicon-list"></i><br>
                                View Orders
                            </a>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/user/index'])) ?>" class="btn btn-success btn-lg">
                                <i class="glyphicon glyphicon-user"></i><br>
                                Manage Users
                            </a>
                        </div>
                        <div class="col-md-6 text-center">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/settings/index'])) ?>" class="btn btn-warning btn-lg">
                                <i class="glyphicon glyphicon-cog"></i><br>
                                Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Sales Chart (Last 30 Days)</h3>
                </div>
                <div class="panel-body">
                    <canvas id="salesChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple chart using HTML5 Canvas
    var canvas = document.getElementById('salesChart');
    var ctx = canvas.getContext('2d');
    
    // Sample data - in real app, this would come from the controller
    var data = <?= json_encode($statistics['sales_data'] ?? []) ?>;
    
    if (data.length > 0) {
        // Draw simple line chart
        ctx.strokeStyle = '#007bff';
        ctx.lineWidth = 2;
        ctx.beginPath();
        
        var maxValue = Math.max(...data.map(d => d.value || 0));
        var stepX = canvas.width / data.length;
        var stepY = canvas.height / maxValue;
        
        data.forEach(function(point, index) {
            var x = index * stepX;
            var y = canvas.height - (point.value || 0) * stepY;
            
            if (index === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });
        
        ctx.stroke();
    } else {
        // No data message
        ctx.font = '16px Arial';
        ctx.fillStyle = '#666';
        ctx.textAlign = 'center';
        ctx.fillText('No sales data available', canvas.width / 2, canvas.height / 2);
    }
});
</script>
