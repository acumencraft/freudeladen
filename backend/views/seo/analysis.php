<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $analysis array */

$this->title = 'SEO Analysis & Recommendations';
$this->params['breadcrumbs'][] = ['label' => 'SEO Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seo-analysis">

    <!-- SEO Score Overview -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> SEO Analysis Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Comprehensive SEO Audit Results</h4>
                            <p class="text-muted">
                                Our analysis covers meta tags, content optimization, technical SEO, and provides actionable recommendations to improve your search engine visibility.
                            </p>
                        </div>
                        <div class="col-md-4 text-center">
                            <?= Html::a('<i class="fas fa-sync"></i> Run New Analysis', ['analysis'], [
                                'class' => 'btn btn-success btn-lg'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analysis Categories -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-tags"></i> Meta Tags Analysis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Site Title
                            <?php if ($analysis['meta_tags']['title_configured']): ?>
                                <span class="badge badge-success">✓ Configured</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ Missing</span>
                            <?php endif; ?>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Meta Description
                            <?php if ($analysis['meta_tags']['description_configured']): ?>
                                <span class="badge badge-success">✓ Configured</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ Missing</span>
                            <?php endif; ?>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Meta Keywords
                            <?php if ($analysis['meta_tags']['keywords_configured']): ?>
                                <span class="badge badge-success">✓ Configured</span>
                            <?php else: ?>
                                <span class="badge badge-warning">⚠ Optional</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-file-alt"></i> Content Analysis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary"><?= number_format($analysis['content']['pages_optimized']) ?></h3>
                        <p class="mb-0">SEO Optimized Pages</p>
                    </div>
                    
                    <div class="text-center mb-3">
                        <h3 class="text-info"><?= number_format($analysis['content']['blog_posts']) ?></h3>
                        <p class="mb-0">Published Blog Posts</p>
                    </div>
                    
                    <div class="progress">
                        <?php 
                        $contentScore = 0;
                        if ($analysis['content']['pages_optimized'] > 10) $contentScore += 50;
                        elseif ($analysis['content']['pages_optimized'] > 5) $contentScore += 30;
                        if ($analysis['content']['blog_posts'] > 10) $contentScore += 50;
                        elseif ($analysis['content']['blog_posts'] > 5) $contentScore += 30;
                        ?>
                        <div class="progress-bar bg-success" style="width: <?= $contentScore ?>%">
                            <?= $contentScore ?>% Content Score
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cogs"></i> Technical SEO
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            XML Sitemap
                            <?php if ($analysis['technical']['sitemap_exists']): ?>
                                <span class="badge badge-success">✓ Generated</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ Missing</span>
                            <?php endif; ?>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Robots.txt
                            <?php if ($analysis['technical']['robots_exists']): ?>
                                <span class="badge badge-success">✓ Generated</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ Missing</span>
                            <?php endif; ?>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Analytics
                            <?php if ($analysis['technical']['analytics_configured']): ?>
                                <span class="badge badge-success">✓ Configured</span>
                            <?php else: ?>
                                <span class="badge badge-warning">⚠ Missing</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb"></i> SEO Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($analysis['recommendations'])): ?>
                        <div class="row">
                            <?php foreach ($analysis['recommendations'] as $recommendation): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-<?= $recommendation['type'] === 'error' ? 'danger' : ($recommendation['type'] === 'warning' ? 'warning' : 'info') ?>">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-<?= $recommendation['type'] === 'error' ? 'exclamation-circle text-danger' : ($recommendation['type'] === 'warning' ? 'exclamation-triangle text-warning' : 'info-circle text-info') ?>"></i>
                                                <?= Html::encode($recommendation['title']) ?>
                                            </h6>
                                            <p class="card-text small"><?= Html::encode($recommendation['description']) ?></p>
                                            <?php if (isset($recommendation['action'])): ?>
                                                <div class="text-right">
                                                    <small class="text-muted"><?= Html::encode($recommendation['action']) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>Excellent SEO Configuration!</h4>
                            <p>Your website's SEO is well-configured. Keep monitoring and updating your content regularly.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Checklist -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-info">
                <div class="card-header bg-light">
                    <h5 class="card-title">
                        <i class="fas fa-tasks"></i> SEO Improvement Checklist
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fas fa-medal text-warning"></i> High Priority</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" <?= $analysis['meta_tags']['title_configured'] ? 'checked' : '' ?> disabled>
                                <label class="form-check-label small">Configure unique page titles</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" <?= $analysis['meta_tags']['description_configured'] ? 'checked' : '' ?> disabled>
                                <label class="form-check-label small">Write compelling meta descriptions</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" <?= $analysis['technical']['sitemap_exists'] ? 'checked' : '' ?> disabled>
                                <label class="form-check-label small">Generate XML sitemap</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" <?= $analysis['technical']['robots_exists'] ? 'checked' : '' ?> disabled>
                                <label class="form-check-label small">Create robots.txt file</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <h6><i class="fas fa-star text-info"></i> Medium Priority</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" <?= $analysis['technical']['analytics_configured'] ? 'checked' : '' ?> disabled>
                                <label class="form-check-label small">Setup Google Analytics</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" <?= $analysis['content']['pages_optimized'] > 5 ? 'checked' : '' ?> disabled>
                                <label class="form-check-label small">Optimize important pages</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" disabled>
                                <label class="form-check-label small">Configure Open Graph tags</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" disabled>
                                <label class="form-check-label small">Submit sitemap to search engines</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <h6><i class="fas fa-plus text-success"></i> Nice to Have</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" disabled>
                                <label class="form-check-label small">Setup Schema markup</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" disabled>
                                <label class="form-check-label small">Optimize images with alt tags</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" disabled>
                                <label class="form-check-label small">Create breadcrumb navigation</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" <?= $analysis['content']['blog_posts'] > 5 ? 'checked' : '' ?> disabled>
                                <label class="form-check-label small">Regular blog content</label>
                            </div>
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
                        <i class="fas fa-rocket"></i> Quick SEO Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <?= Html::a('<i class="fas fa-cogs"></i><br>SEO Settings', ['settings'], [
                                'class' => 'btn btn-primary btn-lg btn-block h-100'
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <?= Html::a('<i class="fas fa-sitemap"></i><br>Generate Sitemap', ['generate-sitemap'], [
                                'class' => 'btn btn-success btn-lg btn-block h-100',
                                'data-method' => 'post'
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <?= Html::a('<i class="fas fa-magic"></i><br>Auto-detect Pages', ['auto-detect-pages'], [
                                'class' => 'btn btn-info btn-lg btn-block h-100'
                            ]) ?>
                        </div>
                        <div class="col-md-3 mb-3">
                            <?= Html::a('<i class="fas fa-plus"></i><br>Add SEO Page', ['create-page'], [
                                'class' => 'btn btn-warning btn-lg btn-block h-100'
                            ]) ?>
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
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.btn-block i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.list-group-item {
    border-left: none;
    border-right: none;
    padding: 0.75rem 0;
}

.form-check {
    margin-bottom: 0.5rem;
}

.form-check-input:disabled {
    opacity: 0.8;
}

.badge {
    font-size: 0.75rem;
}

.progress {
    height: 1.5rem;
}

.alert {
    border-radius: 8px;
}
</style>
