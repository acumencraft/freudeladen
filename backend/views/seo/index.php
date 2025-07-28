<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $totalPages int */
/* @var $totalSettings int */
/* @var $recentPages array */
/* @var $seoHealth array */

$this->title = 'SEO Management Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seo-dashboard">
    
    <!-- SEO Health Score -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-search"></i> SEO Health Score
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="seo-score-circle">
                                <div class="score-value <?= $seoHealth['status'] ?>">
                                    <?= $seoHealth['percentage'] ?>%
                                </div>
                                <div class="score-label">SEO Score</div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar 
                                    <?= $seoHealth['status'] === 'excellent' ? 'bg-success' : 
                                        ($seoHealth['status'] === 'good' ? 'bg-info' : 
                                        ($seoHealth['status'] === 'fair' ? 'bg-warning' : 'bg-danger')) ?>" 
                                     role="progressbar" 
                                     style="width: <?= $seoHealth['percentage'] ?>%" 
                                     aria-valuenow="<?= $seoHealth['percentage'] ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?= $seoHealth['percentage'] ?>%
                                </div>
                            </div>
                            
                            <?php if (!empty($seoHealth['issues'])): ?>
                                <h6>Issues to Address:</h6>
                                <ul class="list-unstyled">
                                    <?php foreach ($seoHealth['issues'] as $issue): ?>
                                        <li><i class="fas fa-exclamation-triangle text-warning"></i> <?= Html::encode($issue) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check-circle"></i> Your SEO configuration looks excellent!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($totalPages) ?></h4>
                            <p class="mb-0">SEO Pages</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
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
                            <h4 class="mb-0"><?= number_format($totalSettings) ?></h4>
                            <p class="mb-0">SEO Settings</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs fa-2x"></i>
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
                            <h4 class="mb-0">
                                <?php if (file_exists(Yii::getAlias('@frontend/web/sitemap.xml'))): ?>
                                    <i class="fas fa-check"></i>
                                <?php else: ?>
                                    <i class="fas fa-times"></i>
                                <?php endif; ?>
                            </h4>
                            <p class="mb-0">XML Sitemap</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-sitemap fa-2x"></i>
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
                            <h4 class="mb-0">
                                <?php if (file_exists(Yii::getAlias('@frontend/web/robots.txt'))): ?>
                                    <i class="fas fa-check"></i>
                                <?php else: ?>
                                    <i class="fas fa-times"></i>
                                <?php endif; ?>
                            </h4>
                            <p class="mb-0">Robots.txt</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-robot fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-bolt"></i> Quick SEO Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <?= Html::a('<i class="fas fa-cogs"></i><br>SEO Settings', ['settings'], ['class' => 'btn btn-primary btn-lg btn-block h-100']) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <?= Html::a('<i class="fas fa-file-alt"></i><br>Manage Pages', ['pages'], ['class' => 'btn btn-success btn-lg btn-block h-100']) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <?= Html::a('<i class="fas fa-sitemap"></i><br>Generate Sitemap', ['generate-sitemap'], [
                                'class' => 'btn btn-info btn-lg btn-block h-100',
                                'data-method' => 'post',
                                'data-confirm' => 'Are you sure you want to generate a new sitemap?'
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <?= Html::a('<i class="fas fa-chart-line"></i><br>SEO Analysis', ['analysis'], ['class' => 'btn btn-warning btn-lg btn-block h-100']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent SEO Pages and Tools -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-clock"></i> Recently Updated SEO Pages
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentPages)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Route</th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentPages as $page): ?>
                                        <tr>
                                            <td>
                                                <code><?= Html::encode($page->route) ?></code>
                                            </td>
                                            <td>
                                                <strong><?= Html::encode($page->title) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $page->priority >= 0.8 ? 'success' : ($page->priority >= 0.5 ? 'info' : 'secondary') ?>">
                                                    <?= $page->priority ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= Yii::$app->formatter->asRelativeTime($page->updated_at) ?>
                                            </td>
                                            <td>
                                                <?= Html::a('<i class="fas fa-edit"></i>', ['update-page', 'id' => $page->id], [
                                                    'class' => 'btn btn-sm btn-outline-primary',
                                                    'title' => 'Edit'
                                                ]) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <?= Html::a('View All SEO Pages', ['pages'], ['class' => 'btn btn-outline-primary']) ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No SEO pages found. 
                            <?= Html::a('Create your first SEO page', ['create-page'], ['class' => 'alert-link']) ?> or 
                            <?= Html::a('auto-detect pages', ['auto-detect-pages'], ['class' => 'alert-link']) ?>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-tools"></i> SEO Tools
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Auto-detect Pages</h6>
                                <small class="text-muted">Automatically find and add pages for SEO optimization</small>
                            </div>
                            <?= Html::a('<i class="fas fa-magic"></i>', ['auto-detect-pages'], [
                                'class' => 'btn btn-sm btn-primary',
                                'title' => 'Auto-detect Pages'
                            ]) ?>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Sitemap Management</h6>
                                <small class="text-muted">Generate and manage XML sitemaps</small>
                            </div>
                            <?= Html::a('<i class="fas fa-sitemap"></i>', ['sitemap'], [
                                'class' => 'btn btn-sm btn-success',
                                'title' => 'Manage Sitemap'
                            ]) ?>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Generate Robots.txt</h6>
                                <small class="text-muted">Create robots.txt file for search engines</small>
                            </div>
                            <?= Html::a('<i class="fas fa-robot"></i>', ['generate-robots'], [
                                'class' => 'btn btn-sm btn-info',
                                'data-method' => 'post',
                                'title' => 'Generate Robots.txt'
                            ]) ?>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">SEO Analysis</h6>
                                <small class="text-muted">Comprehensive SEO audit and recommendations</small>
                            </div>
                            <?= Html::a('<i class="fas fa-chart-line"></i>', ['analysis'], [
                                'class' => 'btn btn-sm btn-warning',
                                'title' => 'Run Analysis'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.seo-score-circle {
    text-align: center;
}

.score-value {
    font-size: 3rem;
    font-weight: bold;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    line-height: 120px;
    margin: 0 auto 10px;
    color: white;
}

.score-value.excellent {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.score-value.good {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
}

.score-value.fair {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.score-value.poor {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
}

.score-label {
    font-weight: 600;
    color: #6c757d;
}

.btn-block.h-100 {
    white-space: normal;
    padding: 1.5rem;
    text-align: center;
}

.btn-block i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}
</style>
