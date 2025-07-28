<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BlogCategory */

$this->title = 'Create Blog Category';
$this->params['breadcrumbs'][] = ['label' => 'Blog Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-category-create">

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

                    <div class="form-group">
                        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6>Category Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Use descriptive names</li>
                        <li><i class="fas fa-check text-success"></i> Keep names concise</li>
                        <li><i class="fas fa-check text-success"></i> Slug will be auto-generated</li>
                        <li><i class="fas fa-check text-success"></i> Categories organize blog posts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Auto-generate slug from name
$('#blogcategory-name').on('blur', function() {
    var name = $(this).val();
    var slug = $('#blogcategory-slug').val();
    
    if (name && !slug) {
        var generatedSlug = name.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        $('#blogcategory-slug').val(generatedSlug);
    }
});
</script>
