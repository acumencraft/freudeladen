<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $sitemapStats array */
/* @var $sitemapExists bool */
/* @var $sitemapLastModified int|null */

$this->title = 'Sitemap Management';
$this->params['breadcrumbs'][] = ['label' => 'SEO Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sitemap-management">

    <!-- Sitemap Status -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sitemap"></i> XML Sitemap Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <?php if ($sitemapExists): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> 
                                    <strong>Sitemap Generated</strong><br>
                                    Last updated: <?= Yii::$app->formatter->asDatetime($sitemapLastModified) ?><br>
                                    <small class="text-muted">
                                        <?= Html::a('View Sitemap', Yii::$app->request->hostInfo . '/sitemap.xml', [
                                            'target' => '_blank',
                                            'class' => 'alert-link'
                                        ]) ?>
                                    </small>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>No Sitemap Found</strong><br>
                                    Generate an XML sitemap to help search engines discover your content.
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-center">
                            <?= Html::a('<i class="fas fa-cogs"></i> Generate New Sitemap', ['generate-sitemap'], [
                                'class' => 'btn btn-primary btn-lg',
                                'data-method' => 'post',
                                'data-confirm' => 'This will overwrite the existing sitemap. Continue?'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sitemap Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= number_format($sitemapStats['totalUrls']) ?></h3>
                    <p class="mb-0">Total URLs</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= number_format($sitemapStats['highPriorityUrls']) ?></h3>
                    <p class="mb-0">High Priority</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= number_format($sitemapStats['dailyUpdates']) ?></h3>
                    <p class="mb-0">Daily Updates</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= number_format($sitemapStats['weeklyUpdates']) ?></h3>
                    <p class="mb-0">Weekly Updates</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sitemap Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-tools"></i> Sitemap Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <?= Html::a('<i class="fas fa-plus"></i><br>Add SEO Page', ['create-page'], [
                                    'class' => 'btn btn-success btn-lg btn-block h-100'
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <?= Html::a('<i class="fas fa-magic"></i><br>Auto-detect Pages', ['auto-detect-pages'], [
                                    'class' => 'btn btn-info btn-lg btn-block h-100'
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <?= Html::a('<i class="fas fa-list"></i><br>Manage SEO Pages', ['pages'], [
                                    'class' => 'btn btn-primary btn-lg btn-block h-100'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Engine Submission -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-info">
                <div class="card-header bg-light">
                    <h5 class="card-title">
                        <i class="fas fa-globe"></i> Search Engine Submission
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fab fa-google text-danger"></i> Google Search Console</h6>
                            <p class="text-muted small">Submit your sitemap to Google for faster indexing:</p>
                            <ol class="small">
                                <li>Go to <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                                <li>Select your property</li>
                                <li>Navigate to Sitemaps → Add a new sitemap</li>
                                <li>Enter: <code>sitemap.xml</code></li>
                                <li>Click Submit</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fab fa-microsoft text-info"></i> Bing Webmaster Tools</h6>
                            <p class="text-muted small">Submit your sitemap to Bing:</p>
                            <ol class="small">
                                <li>Go to <a href="https://www.bing.com/webmasters" target="_blank">Bing Webmaster Tools</a></li>
                                <li>Select your site</li>
                                <li>Navigate to Sitemaps</li>
                                <li>Enter sitemap URL: <code><?= Yii::$app->request->hostInfo ?>/sitemap.xml</code></li>
                                <li>Click Submit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sitemap Guidelines -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-warning">
                <div class="card-header bg-light">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle"></i> Sitemap Best Practices
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-check text-success"></i> Do's</h6>
                            <ul class="small">
                                <li>Include only indexable pages</li>
                                <li>Use absolute URLs</li>
                                <li>Keep sitemap under 50MB</li>
                                <li>Update sitemap regularly</li>
                                <li>Use priority values appropriately</li>
                                <li>Set accurate change frequencies</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-times text-danger"></i> Don'ts</h6>
                            <ul class="small">
                                <li>Don't include blocked URLs</li>
                                <li>Don't use relative URLs</li>
                                <li>Don't include redirected pages</li>
                                <li>Don't set all priorities to 1.0</li>
                                <li>Don't include non-canonical URLs</li>
                                <li>Don't exceed 50,000 URLs</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fas fa-star text-warning"></i> Tips</h6>
                            <ul class="small">
                                <li>Homepage should have priority 1.0</li>
                                <li>Important pages: 0.8-0.9</li>
                                <li>Regular content: 0.5-0.7</li>
                                <li>Archive pages: 0.1-0.4</li>
                                <li>News content: daily frequency</li>
                                <li>Static pages: monthly/yearly</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.btn-block.h-100 {
    white-space: normal;
    padding: 1.5rem;
    text-align: center;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-block i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.85rem;
}

.alert {
    border-radius: 8px;
}
</style>
