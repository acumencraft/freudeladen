<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $generalSettings array */
/* @var $metaSettings array */
/* @var $socialSettings array */
/* @var $analyticsSettings array */
/* @var $sitemapSettings array */

$this->title = 'SEO Settings';
$this->params['breadcrumbs'][] = ['label' => 'SEO Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="seo-settings">
    
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-8\">{input}</div>\n<div class=\"col-sm-12\">{error}</div>",
            'labelOptions' => ['class' => 'col-sm-4 control-label'],
        ],
    ]); ?>

    <!-- General SEO Settings -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-globe"></i> General SEO Settings
            </h5>
        </div>
        <div class="card-body">
            <?php foreach ($generalSettings as $setting): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="<?= $setting->key ?>">
                        <strong><?= Html::encode($setting->key) ?></strong>
                        <?php if ($setting->description): ?>
                            <br><small class="text-muted"><?= Html::encode($setting->description) ?></small>
                        <?php endif; ?>
                    </label>
                    <div class="col-sm-8">
                        <?php if (in_array($setting->key, ['site_description'])): ?>
                            <?= Html::textarea("settings[{$setting->key}]", $setting->value, [
                                'class' => 'form-control',
                                'rows' => 3,
                                'placeholder' => 'Enter ' . $setting->key
                            ]) ?>
                        <?php else: ?>
                            <?= Html::textInput("settings[{$setting->key}]", $setting->value, [
                                'class' => 'form-control',
                                'placeholder' => 'Enter ' . $setting->key
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Meta Tags Settings -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-tags"></i> Meta Tags Settings
            </h5>
        </div>
        <div class="card-body">
            <?php foreach ($metaSettings as $setting): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="<?= $setting->key ?>">
                        <strong><?= Html::encode($setting->key) ?></strong>
                        <?php if ($setting->description): ?>
                            <br><small class="text-muted"><?= Html::encode($setting->description) ?></small>
                        <?php endif; ?>
                    </label>
                    <div class="col-sm-8">
                        <?= Html::textInput("settings[{$setting->key}]", $setting->value, [
                            'class' => 'form-control',
                            'placeholder' => 'Enter ' . $setting->key
                        ]) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Social Media Settings -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-share-alt"></i> Social Media Settings
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Configure Open Graph and Twitter Card settings for better social media sharing.
            </div>
            
            <?php foreach ($socialSettings as $setting): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="<?= $setting->key ?>">
                        <strong><?= Html::encode($setting->key) ?></strong>
                        <?php if ($setting->description): ?>
                            <br><small class="text-muted"><?= Html::encode($setting->description) ?></small>
                        <?php endif; ?>
                    </label>
                    <div class="col-sm-8">
                        <?= Html::textInput("settings[{$setting->key}]", $setting->value, [
                            'class' => 'form-control',
                            'placeholder' => 'Enter ' . $setting->key
                        ]) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Analytics Settings -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-line"></i> Analytics & Tracking
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> These tracking codes will be automatically included in your website's header.
            </div>
            
            <?php foreach ($analyticsSettings as $setting): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="<?= $setting->key ?>">
                        <strong><?= Html::encode($setting->key) ?></strong>
                        <?php if ($setting->description): ?>
                            <br><small class="text-muted"><?= Html::encode($setting->description) ?></small>
                        <?php endif; ?>
                    </label>
                    <div class="col-sm-8">
                        <?= Html::textInput("settings[{$setting->key}]", $setting->value, [
                            'class' => 'form-control',
                            'placeholder' => $setting->key === 'google_analytics' ? 'GA4-XXXXXXXXX-X' : 
                                          ($setting->key === 'google_tag_manager' ? 'GTM-XXXXXXX' : 
                                          ($setting->key === 'facebook_pixel' ? '123456789012345' : 'Enter ' . $setting->key))
                        ]) ?>
                        
                        <?php if ($setting->key === 'google_analytics'): ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Get your Google Analytics 4 tracking ID from 
                                <a href="https://analytics.google.com" target="_blank">Google Analytics</a>
                            </small>
                        <?php elseif ($setting->key === 'google_tag_manager'): ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Get your GTM container ID from 
                                <a href="https://tagmanager.google.com" target="_blank">Google Tag Manager</a>
                            </small>
                        <?php elseif ($setting->key === 'facebook_pixel'): ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Get your Facebook Pixel ID from 
                                <a href="https://business.facebook.com/events_manager" target="_blank">Facebook Events Manager</a>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sitemap Settings -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-sitemap"></i> Sitemap Settings
            </h5>
        </div>
        <div class="card-body">
            <?php foreach ($sitemapSettings as $setting): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="<?= $setting->key ?>">
                        <strong><?= Html::encode($setting->key) ?></strong>
                        <?php if ($setting->description): ?>
                            <br><small class="text-muted"><?= Html::encode($setting->description) ?></small>
                        <?php endif; ?>
                    </label>
                    <div class="col-sm-8">
                        <?php if ($setting->key === 'sitemap_frequency'): ?>
                            <?= Html::dropDownList("settings[{$setting->key}]", $setting->value, [
                                'always' => 'Always',
                                'hourly' => 'Hourly',
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                                'never' => 'Never'
                            ], ['class' => 'form-control']) ?>
                        <?php elseif ($setting->key === 'sitemap_auto_generate'): ?>
                            <?= Html::dropDownList("settings[{$setting->key}]", $setting->value, [
                                '0' => 'Disabled',
                                '1' => 'Enabled'
                            ], ['class' => 'form-control']) ?>
                        <?php else: ?>
                            <?= Html::textInput("settings[{$setting->key}]", $setting->value, [
                                'class' => 'form-control',
                                'placeholder' => 'Enter ' . $setting->key
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Save Button -->
    <div class="card">
        <div class="card-body text-center">
            <?= Html::submitButton('<i class="fas fa-save"></i> Save SEO Settings', [
                'class' => 'btn btn-primary btn-lg'
            ]) ?>
            
            <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Dashboard', ['index'], [
                'class' => 'btn btn-secondary btn-lg ml-2'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
.form-group.row {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.form-group.row:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.control-label strong {
    color: #495057;
    font-size: 0.95rem;
}

.card-header h5 {
    font-weight: 600;
}

.alert {
    border-radius: 8px;
}

.form-control {
    border-radius: 6px;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 1.1rem;
    border-radius: 8px;
}
</style>
