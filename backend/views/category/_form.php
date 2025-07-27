<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Category;

/** @var yii\web\View $this */
/** @var common\models\Category $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'parent_id')->dropDownList(
        ArrayHelper::map(
            Category::find()->where(['!=', 'id', $model->id ?: 0])->orderBy('name')->all(),
            'id',
            'name'
        ),
        ['prompt' => 'Hauptkategorie (keine übergeordnete Kategorie)']
    ) ?>

    <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'is_active')->checkbox() ?>

    <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'meta_description')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Erstellen' : 'Aktualisieren', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Abbrechen', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    $('#category-name').on('blur', function() {
        var name = $(this).val();
        if (name && !$('#category-slug').val()) {
            var slug = name.toLowerCase()
                .replace(/ä/g, 'ae')
                .replace(/ö/g, 'oe')
                .replace(/ü/g, 'ue')
                .replace(/ß/g, 'ss')
                .replace(/[^a-z0-9]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            $('#category-slug').val(slug);
        }
    });
");
?>
