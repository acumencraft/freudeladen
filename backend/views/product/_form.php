<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Category;

/** @var yii\web\View $this */
/** @var common\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'short_description')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'category_id')->dropDownList(
        ArrayHelper::map(Category::find()->orderBy('name')->all(), 'id', 'name'),
        ['prompt' => 'Kategorie wählen']
    ) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'price')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sale_price')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'stock')->textInput(['type' => 'number', 'min' => '0']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'step' => '0.01', 'min' => '0']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'dimensions')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'is_active')->checkbox() ?>
    
    <?= $form->field($model, 'is_featured')->checkbox() ?>

    <h4>SEO Einstellungen</h4>
    
    <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'meta_description')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Erstellen' : 'Aktualisieren', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Abbrechen', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    $('#product-name').on('blur', function() {
        var name = $(this).val();
        if (name && !$('#product-slug').val()) {
            var slug = name.toLowerCase()
                .replace(/ä/g, 'ae')
                .replace(/ö/g, 'oe')
                .replace(/ü/g, 'ue')
                .replace(/ß/g, 'ss')
                .replace(/[^a-z0-9]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            $('#product-slug').val(slug);
        }
    });
");
?>
