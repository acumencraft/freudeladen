<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\SeoPage;

/* @var $this yii\web\View */
/* @var $model common\models\SeoPage */

$this->title = 'Create SEO Page';
$this->params['breadcrumbs'][] = ['label' => 'SEO Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'SEO Pages', 'url' => ['pages']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seo-page-create">

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus"></i> Create New SEO Page
                    </h5>
                </div>
                <div class="card-body">
                    
                    <?php $form = ActiveForm::begin(); ?>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'route')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'e.g., /products/category/electronics',
                                'class' => 'form-control'
                            ])->hint('Enter the route/URL path for this page (starting with /)') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'title')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'Enter page title (max 60 characters recommended)',
                                'class' => 'form-control'
                            ])->hint('The title that appears in search results and browser tabs') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'description')->textarea([
                                'rows' => 3,
                                'placeholder' => 'Enter meta description (150-160 characters recommended)',
                                'class' => 'form-control'
                            ])->hint('A brief description that appears in search results') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'keywords')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'keyword1, keyword2, keyword3',
                                'class' => 'form-control'
                            ])->hint('Comma-separated keywords relevant to this page') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'priority')->dropDownList([
                                '1.0' => '1.0 - Highest (Homepage)',
                                '0.9' => '0.9 - Very High',
                                '0.8' => '0.8 - High (Important pages)',
                                '0.7' => '0.7 - Above Average',
                                '0.6' => '0.6 - Average (Regular content)',
                                '0.5' => '0.5 - Below Average',
                                '0.4' => '0.4 - Low (Archive pages)',
                                '0.3' => '0.3 - Very Low',
                                '0.2' => '0.2 - Lowest',
                            ], [
                                'class' => 'form-control',
                                'prompt' => 'Select Priority'
                            ])->hint('Higher priority pages are crawled more frequently') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'changefreq')->dropDownList(SeoPage::getChangeFreqOptions(), [
                                'class' => 'form-control',
                                'prompt' => 'Select Change Frequency'
                            ])->hint('How frequently the page content changes') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'robots')->dropDownList([
                                'index, follow' => 'index, follow (Default)',
                                'index, nofollow' => 'index, nofollow',
                                'noindex, follow' => 'noindex, follow',
                                'noindex, nofollow' => 'noindex, nofollow',
                            ], [
                                'class' => 'form-control'
                            ])->hint('Instructions for search engine crawlers') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'is_active')->dropDownList([
                                1 => 'Active',
                                0 => 'Inactive'
                            ], [
                                'class' => 'form-control'
                            ])->hint('Whether this SEO configuration is active') ?>
                        </div>
                    </div>

                    <hr>

                    <h6><i class="fas fa-share-alt"></i> Open Graph Settings (Optional)</h6>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'og_title')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'Leave empty to use page title',
                                'class' => 'form-control'
                            ])->hint('Title for social media sharing (Facebook, LinkedIn)') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'og_description')->textarea([
                                'rows' => 2,
                                'placeholder' => 'Leave empty to use meta description',
                                'class' => 'form-control'
                            ])->hint('Description for social media sharing') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'og_image')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'https://example.com/image.jpg',
                                'class' => 'form-control'
                            ])->hint('Full URL to image for social media sharing') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'canonical_url')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'https://example.com/canonical-url',
                                'class' => 'form-control'
                            ])->hint('Canonical URL to prevent duplicate content issues') ?>
                        </div>
                    </div>

                    <div class="form-group text-center mt-4">
                        <?= Html::submitButton('<i class="fas fa-save"></i> Create SEO Page', [
                            'class' => 'btn btn-primary btn-lg'
                        ]) ?>
                        <?= Html::a('<i class="fas fa-times"></i> Cancel', ['pages'], [
                            'class' => 'btn btn-secondary btn-lg ml-2'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> SEO Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-route text-primary"></i> Route Examples</h6>
                        <ul class="small">
                            <li><code>/</code> - Homepage</li>
                            <li><code>/about</code> - About page</li>
                            <li><code>/products</code> - Products listing</li>
                            <li><code>/blog/post-title</code> - Blog post</li>
                            <li><code>/category/electronics</code> - Category page</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6><i class="fas fa-star text-warning"></i> Priority Guidelines</h6>
                        <ul class="small">
                            <li><strong>1.0:</strong> Homepage only</li>
                            <li><strong>0.8-0.9:</strong> Main category pages, important landing pages</li>
                            <li><strong>0.6-0.7:</strong> Product pages, blog posts</li>
                            <li><strong>0.4-0.5:</strong> Archive pages, tags</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6><i class="fas fa-clock text-success"></i> Change Frequency</h6>
                        <ul class="small">
                            <li><strong>Daily:</strong> News, blogs, homepages</li>
                            <li><strong>Weekly:</strong> Product pages, categories</li>
                            <li><strong>Monthly:</strong> About, contact, policies</li>
                            <li><strong>Yearly:</strong> Archive, old content</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Note:</strong> Priority and change frequency are hints for search engines, not guarantees of crawling behavior.
                        </small>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<style>
.form-group {
    margin-bottom: 1.5rem;
}

.hint-block {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.btn-lg {
    padding: 12px 24px;
    font-size: 1.1rem;
    border-radius: 8px;
}

code {
    background-color: #f8f9fa;
    padding: 1px 4px;
    border-radius: 3px;
    font-size: 0.85rem;
}

.alert {
    border-radius: 8px;
}
</style>
