<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ShippingMethod;

/* @var $this yii\web\View */
/* @var $model common\models\ShippingMethod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shipping-method-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Basic Information</h3>
                </div>
                <div class="card-body">
                    
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

                    <?= $form->field($model, 'provider')->dropDownList(
                        ShippingMethod::getProviderOptions(),
                        ['prompt' => 'Select Provider']
                    ) ?>

                    <!-- Settings section -->
                    <div class="form-group">
                        <label class="control-label">Provider Settings</label>
                        <div id="settings-container">
                            <?php
                            $settings = $model->getSettingsArray();
                            if (empty($settings)) {
                                $settings = ['api_key' => '', 'api_secret' => '', 'test_mode' => false];
                            }
                            ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <?= Html::textInput('settings[api_key]', $settings['api_key'] ?? '', [
                                        'class' => 'form-control',
                                        'placeholder' => 'API Key',
                                        'id' => 'settings-api-key'
                                    ]) ?>
                                    <small class="form-text text-muted">Provider API key (if required)</small>
                                </div>
                                <div class="col-md-6">
                                    <?= Html::textInput('settings[api_secret]', $settings['api_secret'] ?? '', [
                                        'class' => 'form-control',
                                        'placeholder' => 'API Secret',
                                        'id' => 'settings-api-secret'
                                    ]) ?>
                                    <small class="form-text text-muted">Provider API secret (if required)</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <?= Html::checkbox('settings[test_mode]', $settings['test_mode'] ?? false, [
                                            'class' => 'form-check-input',
                                            'id' => 'settings-test-mode'
                                        ]) ?>
                                        <label class="form-check-label" for="settings-test-mode">
                                            Test Mode
                                        </label>
                                        <small class="form-text text-muted">Enable test mode for this shipping method</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?= Html::textInput('settings[tracking_url]', $settings['tracking_url'] ?? '', [
                                        'class' => 'form-control',
                                        'placeholder' => 'Tracking URL Template',
                                        'id' => 'settings-tracking-url'
                                    ]) ?>
                                    <small class="form-text text-muted">Use {tracking_number} placeholder</small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <?= Html::textInput('settings[min_delivery_days]', $settings['min_delivery_days'] ?? '', [
                                        'class' => 'form-control',
                                        'placeholder' => 'Min Delivery Days',
                                        'type' => 'number',
                                        'min' => 0
                                    ]) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= Html::textInput('settings[max_delivery_days]', $settings['max_delivery_days'] ?? '', [
                                        'class' => 'form-control',
                                        'placeholder' => 'Max Delivery Days',
                                        'type' => 'number',
                                        'min' => 0
                                    ]) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= Html::textInput('settings[max_weight]', $settings['max_weight'] ?? '', [
                                        'class' => 'form-control',
                                        'placeholder' => 'Max Weight (kg)',
                                        'type' => 'number',
                                        'step' => '0.1',
                                        'min' => 0
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status & Order</h3>
                </div>
                <div class="card-body">
                    
                    <?= $form->field($model, 'status')->dropDownList([
                        ShippingMethod::STATUS_ACTIVE => 'Active',
                        ShippingMethod::STATUS_INACTIVE => 'Inactive'
                    ]) ?>

                    <?= $form->field($model, 'sort_order')->textInput(['type' => 'number', 'min' => 0]) ?>
                    
                    <?php if (!$model->isNewRecord): ?>
                    <div class="form-group">
                        <label>Statistics</label>
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-shipping-fast"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Shipping Rates</span>
                                <span class="info-box-number"><?= $model->getRatesCount() ?></span>
                            </div>
                        </div>
                        
                        <?php if ($model->getRatesCount() > 0): ?>
                        <div class="mt-2">
                            <?= Html::a('Manage Rates', ['/shipping-rate/index', 'method_id' => $model->id], [
                                'class' => 'btn btn-sm btn-info'
                            ]) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        
        <?php if (!$model->isNewRecord): ?>
            <?= Html::a('View', ['view', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    // Dynamic settings based on provider
    $('#shippingmethod-provider').change(function() {
        var provider = $(this).val();
        updateSettingsFields(provider);
    });
    
    function updateSettingsFields(provider) {
        // This could be expanded to show provider-specific settings
        console.log('Provider changed to:', provider);
    }
");
?>
