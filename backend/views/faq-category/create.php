<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FaqCategory */

$this->title = 'Create FAQ Category';
$this->params['breadcrumbs'][] = ['label' => 'FAQ Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="faq-category-create">

    <div class="row">
        <div class="col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Category Details</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true])->hint('Leave empty to auto-generate from name') ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 4])->hint('Optional description for this category') ?>

                    <div class="form-group">
                        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Settings -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Settings</h6>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>
                    
                    <?= $form->field($model, 'sort_order')->textInput(['type' => 'number', 'min' => 0])->hint('Higher numbers appear first') ?>

                    <?= $form->field($model, 'status')->dropDownList([
                        1 => 'Active',
                        0 => 'Inactive'
                    ]) ?>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="card">
                <div class="card-header">
                    <h6>Category Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success"></i> Use descriptive names</li>
                        <li><i class="fas fa-check text-success"></i> Keep names concise</li>
                        <li><i class="fas fa-check text-success"></i> Slug will be auto-generated</li>
                        <li><i class="fas fa-check text-success"></i> Categories organize related FAQs</li>
                        <li><i class="fas fa-check text-success"></i> Set appropriate sort order</li>
                    </ul>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<script>
// Auto-generate slug from name
$('#faqcategory-name').on('blur', function() {
    var name = $(this).val();
    var slug = $('#faqcategory-slug').val();
    
    if (name && !slug) {
        var generatedSlug = name.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        $('#faqcategory-slug').val(generatedSlug);
    }
});
</script>
