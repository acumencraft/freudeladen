<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\BlogPost */
/* @var $categories array */
/* @var $tags array */

$this->title = 'Create Blog Post';
$this->params['breadcrumbs'][] = ['label' => 'Blog Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-post-create">

    <div class="row">
        <div class="col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Blog Post Details</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true])->hint('Leave empty to auto-generate from title') ?>

                    <?= $form->field($model, 'excerpt')->textarea(['rows' => 3])->hint('Short description for blog post previews') ?>

                    <?= $form->field($model, 'content')->textarea(['rows' => 15])->hint('Blog post content') ?>

                    <div class="form-group">
                        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Publishing Options -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Publishing Options</h6>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                    
                    <?= $form->field($model, 'status')->dropDownList([
                        0 => 'Draft',
                        1 => 'Published'
                    ]) ?>

                    <?= $form->field($model, 'published_at')->input('datetime-local') ?>
                </div>
            </div>

            <!-- Category & Tags -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Category & Tags</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'category_id')->dropDownList(
                        ['' => 'Select Category...'] + $categories,
                        ['prompt' => 'Select Category...']
                    ) ?>

                    <div class="form-group">
                        <?= Html::label('Tags', 'tags') ?>
                        <?= Html::checkboxList('tags', [], $tags, [
                            'class' => 'form-check',
                            'item' => function ($index, $label, $name, $checked, $value) {
                                return '<div class="form-check">' .
                                    Html::checkbox($name, $checked, [
                                        'value' => $value,
                                        'class' => 'form-check-input',
                                        'id' => 'tag_' . $value
                                    ]) .
                                    Html::label($label, 'tag_' . $value, ['class' => 'form-check-label']) .
                                    '</div>';
                            }
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Featured Image</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'featured_image')->fileInput(['accept' => 'image/*'])->label('Upload Image') ?>
                    <small class="text-muted">Recommended size: 800x400px</small>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="card">
                <div class="card-header">
                    <h6>SEO Settings</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'meta_title')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'meta_description')->textarea(['rows' => 3, 'maxlength' => 160]) ?>
                    <small class="text-muted">Max 160 characters for best SEO results</small>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<script>
// Auto-generate slug from title
$('#blogpost-title').on('blur', function() {
    var title = $(this).val();
    var slug = $('#blogpost-slug').val();
    
    if (title && !slug) {
        var generatedSlug = title.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        $('#blogpost-slug').val(generatedSlug);
    }
});

// Auto-generate meta title from title
$('#blogpost-title').on('blur', function() {
    var title = $(this).val();
    var metaTitle = $('#blogpost-meta_title').val();
    
    if (title && !metaTitle) {
        $('#blogpost-meta_title').val(title);
    }
});
</script>
