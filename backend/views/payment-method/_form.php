<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-method-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => '<div class="col-sm-2">{label}</div><div class="col-sm-10">{input}{error}</div>',
            'labelOptions' => ['class' => 'control-label'],
        ],
    ]); ?>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Basic Information</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

            <?= $form->field($model, 'type')->dropDownList(PaymentMethod::getTypeOptions(), ['prompt' => 'Select payment type...']) ?>

            <?= $form->field($model, 'provider')->dropDownList(PaymentMethod::getProviderOptions(), ['prompt' => 'Select provider...']) ?>

            <?= $form->field($model, 'is_active')->checkbox([
                'template' => '<div class="col-sm-offset-2 col-sm-10"><div class="checkbox">{input} {label}</div>{error}</div>',
            ]) ?>

            <?= $form->field($model, 'sort_order')->textInput(['type' => 'number', 'min' => 0]) ?>
        </div>
    </div>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Fee Configuration</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'fee_type')->dropDownList(PaymentMethod::getFeeTypeOptions(), [
                'id' => 'fee-type-select',
                'prompt' => 'Select fee type...'
            ]) ?>

            <div id="fee-fixed-group" style="display: none;">
                <?= $form->field($model, 'fee_fixed')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0]) ?>
            </div>

            <div id="fee-percentage-group" style="display: none;">
                <?= $form->field($model, 'fee_percentage')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0, 'max' => 100]) ?>
            </div>
        </div>
    </div>

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Transaction Limits</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'min_amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0]) ?>

            <?= $form->field($model, 'max_amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0]) ?>

            <?= $form->field($model, 'supported_currencies')->textInput([
                'placeholder' => 'EUR,USD,GBP (comma-separated)',
                'data-toggle' => 'tooltip',
                'title' => 'Enter supported currencies separated by commas. Leave empty to support all currencies.'
            ]) ?>
        </div>
    </div>

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Provider Configuration</h3>
            <div class="box-tools pull-right">
                <small class="text-muted">Store API keys and other provider-specific settings</small>
            </div>
        </div>
        <div class="box-body">
            <div id="config-container">
                <?php
                $config = $model->getConfigArray();
                if (empty($config)) {
                    $config = ['api_key' => '', 'secret_key' => '']; // Default fields
                }
                ?>
                <?php foreach ($config as $key => $value): ?>
                    <div class="form-group config-row">
                        <div class="col-sm-2">
                            <input type="text" name="config_keys[]" value="<?= Html::encode($key) ?>" class="form-control" placeholder="Key">
                        </div>
                        <div class="col-sm-8">
                            <?php if (strpos($key, 'secret') !== false || strpos($key, 'key') !== false): ?>
                                <input type="password" name="config_values[]" value="<?= Html::encode($value) ?>" class="form-control" placeholder="Value">
                            <?php else: ?>
                                <input type="text" name="config_values[]" value="<?= Html::encode($value) ?>" class="form-control" placeholder="Value">
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-danger btn-sm remove-config"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" id="add-config" class="btn btn-info btn-sm">
                        <i class="fa fa-plus"></i> Add Configuration Field
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-save"></i> Create Payment Method' : '<i class="fa fa-save"></i> Update Payment Method', [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ]) ?>
            <?= Html::a('<i class="fa fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
// Fee type toggle
$('#fee-type-select').on('change', function() {
    var feeType = $(this).val();
    $('#fee-fixed-group, #fee-percentage-group').hide();
    
    if (feeType === 'fixed' || feeType === 'both') {
        $('#fee-fixed-group').show();
    }
    if (feeType === 'percentage' || feeType === 'both') {
        $('#fee-percentage-group').show();
    }
}).trigger('change');

// Add configuration field
$('#add-config').on('click', function() {
    var html = '<div class=\"form-group config-row\">' +
        '<div class=\"col-sm-2\">' +
            '<input type=\"text\" name=\"config_keys[]\" value=\"\" class=\"form-control\" placeholder=\"Key\">' +
        '</div>' +
        '<div class=\"col-sm-8\">' +
            '<input type=\"text\" name=\"config_values[]\" value=\"\" class=\"form-control\" placeholder=\"Value\">' +
        '</div>' +
        '<div class=\"col-sm-2\">' +
            '<button type=\"button\" class=\"btn btn-danger btn-sm remove-config\"><i class=\"fa fa-trash\"></i></button>' +
        '</div>' +
    '</div>';
    
    $('#config-container').append(html);
});

// Remove configuration field
$(document).on('click', '.remove-config', function() {
    $(this).closest('.config-row').remove();
});

// Toggle password field based on key name
$(document).on('input', 'input[name=\"config_keys[]\"]', function() {
    var key = $(this).val().toLowerCase();
    var valueInput = $(this).closest('.config-row').find('input[name=\"config_values[]\"]');
    
    if (key.indexOf('secret') !== -1 || key.indexOf('key') !== -1 || key.indexOf('password') !== -1) {
        valueInput.attr('type', 'password');
    } else {
        valueInput.attr('type', 'text');
    }
});

// Initialize tooltips
$('[data-toggle=\"tooltip\"]').tooltip();
");
?>
