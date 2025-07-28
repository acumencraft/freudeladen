<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\SeoPage;

/* @var $this yii\web\View */
/* @var $model common\models\SeoPage */
/* @var $isUpdate bool */

$isUpdate = $isUpdate ?? false;
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header <?= $isUpdate ? 'bg-warning' : 'bg-primary' ?> text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-<?= $isUpdate ? 'edit' : 'plus' ?>"></i> 
                    <?= $isUpdate ? 'Update' : 'Create' ?> SEO Page
                </h5>
            </div>
            <div class="card-body">
                
                <?php $form = ActiveForm::begin(); ?>

                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'route')->textInput([
                            'maxlength' => true,
                            'placeholder' => 'e.g., /products/category/electronics',
                            'class' => 'form-control',
                            'readonly' => $isUpdate
                        ])->hint('Enter the route/URL path for this page (starting with /)') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'title')->textInput([
                            'maxlength' => true,
                            'placeholder' => 'Enter page title (max 60 characters recommended)',
                            'class' => 'form-control',
                            'id' => 'seo-title'
                        ])->hint('The title that appears in search results and browser tabs') ?>
                        <div class="character-count" id="title-count">
                            <small class="text-muted">Characters: <span id="title-length">0</span>/60</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'description')->textarea([
                            'rows' => 3,
                            'placeholder' => 'Enter meta description (150-160 characters recommended)',
                            'class' => 'form-control',
                            'id' => 'seo-description'
                        ])->hint('A brief description that appears in search results') ?>
                        <div class="character-count" id="description-count">
                            <small class="text-muted">Characters: <span id="description-length">0</span>/160</small>
                        </div>
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
                            'class' => 'form-control'
                        ])->hint('Higher priority pages are crawled more frequently') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'changefreq')->dropDownList(SeoPage::getChangeFreqOptions(), [
                            'class' => 'form-control'
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
                    <?= Html::submitButton('<i class="fas fa-save"></i> ' . ($isUpdate ? 'Update' : 'Create') . ' SEO Page', [
                        'class' => 'btn btn-' . ($isUpdate ? 'warning' : 'primary') . ' btn-lg'
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
        <!-- SEO Preview -->
        <div class="card border-success mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-search"></i> Search Result Preview
                </h6>
            </div>
            <div class="card-body">
                <div class="search-preview">
                    <div class="preview-url" id="preview-url">
                        <?= Yii::$app->request->hostInfo ?><?= Html::encode($model->route ?: '/your-page') ?>
                    </div>
                    <div class="preview-title" id="preview-title">
                        <?= Html::encode($model->title ?: 'Page Title - Your Site') ?>
                    </div>
                    <div class="preview-description" id="preview-description">
                        <?= Html::encode($model->description ?: 'Meta description appears here in search results...') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Guidelines -->
        <div class="card border-info">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i> SEO Guidelines
                </h6>
            </div>
            <div class="card-body">
                
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
                        <strong>Note:</strong> Keep titles under 60 characters and descriptions between 150-160 characters for optimal display.
                    </small>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.character-count {
    text-align: right;
    margin-top: -10px;
    margin-bottom: 10px;
}

.character-count.warning {
    color: #ffc107 !important;
}

.character-count.danger {
    color: #dc3545 !important;
}

.search-preview {
    font-family: arial, sans-serif;
    background: #fff;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
}

.preview-url {
    color: #1a0dab;
    font-size: 14px;
    margin-bottom: 2px;
}

.preview-title {
    color: #1a0dab;
    font-size: 18px;
    font-weight: normal;
    line-height: 1.2;
    margin-bottom: 4px;
    cursor: pointer;
}

.preview-title:hover {
    text-decoration: underline;
}

.preview-description {
    color: #545454;
    font-size: 13px;
    line-height: 1.4;
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
</style>

<?php
$this->registerJs("
// Character counters
function updateCharacterCount(inputId, counterId, limit) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    const lengthSpan = counter.querySelector('span');
    const count = input.value.length;
    
    lengthSpan.textContent = count;
    
    if (count > limit) {
        counter.className = 'character-count danger';
    } else if (count > limit * 0.9) {
        counter.className = 'character-count warning';
    } else {
        counter.className = 'character-count';
    }
}

function updatePreview() {
    const title = document.getElementById('seo-title').value || 'Page Title - Your Site';
    const description = document.getElementById('seo-description').value || 'Meta description appears here in search results...';
    
    document.getElementById('preview-title').textContent = title;
    document.getElementById('preview-description').textContent = description;
}

// Initialize character counters
document.getElementById('seo-title').addEventListener('input', function() {
    updateCharacterCount('seo-title', 'title-count', 60);
    updatePreview();
});

document.getElementById('seo-description').addEventListener('input', function() {
    updateCharacterCount('seo-description', 'description-count', 160);
    updatePreview();
});

// Initialize on load
updateCharacterCount('seo-title', 'title-count', 60);
updateCharacterCount('seo-description', 'description-count', 160);
updatePreview();
");
?>
