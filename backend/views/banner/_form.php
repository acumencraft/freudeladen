<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

?>

<div class="banner-form">
    <div class="card">
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']) ?>
                </div>
            </div>

            <?= $form->field($model, 'subtitle')->textarea(['rows' => 3]) ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'image_path')->fileInput(['accept' => 'image/*']) ?>
                    <?php if (!$model->isNewRecord && $model->image_path): ?>
                        <div class="mt-2">
                            <label class="form-label">Aktuelles Bild:</label><br>
                            <?= Html::img($model->getImageUrl(), [
                                'class' => 'img-thumbnail',
                                'style' => 'max-width: 200px; max-height: 120px;'
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'link')->textInput(['maxlength' => true, 'placeholder' => 'https://...']) ?>
                    
                    <?= $form->field($model, 'position')->dropDownList(
                        \common\models\Banner::getPositionOptions(),
                        ['prompt' => 'Position auswählen...']
                    ) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'start_date')->input('date') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'end_date')->input('date') ?>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <?= $form->field($model, 'is_active')->checkbox([
                                'template' => '<div class="form-check">{input} {label}</div>{error}',
                                'labelOptions' => ['class' => 'form-check-label'],
                                'inputOptions' => ['class' => 'form-check-input']
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save me-1"></i>' . ($model->isNewRecord ? 'Erstellen' : 'Aktualisieren'), [
                    'class' => 'btn btn-success'
                ]) ?>
                <?= Html::a('Abbrechen', ['index'], [
                    'class' => 'btn btn-outline-secondary'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Preview image before upload
    $('input[type="file"]').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = '<div class="mt-2"><label class="form-label">Vorschau:</label><br><img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 120px;"></div>';
                $(this).parent().find('.preview').remove();
                $(this).parent().append('<div class="preview">' + preview + '</div>');
            }.bind(this);
            reader.readAsDataURL(file);
        }
    });
});
</script>
