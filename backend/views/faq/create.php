<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Faq */
/* @var $categories array */

$this->title = 'Create FAQ';
$this->params['breadcrumbs'][] = ['label' => 'FAQs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="faq-create">

    <div class="row">
        <div class="col-md-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>FAQ Details</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <?= $form->field($model, 'question')->textInput(['maxlength' => true, 'rows' => 2]) ?>

                    <?= $form->field($model, 'answer')->textarea(['rows' => 8])->hint('Provide a detailed answer to the question') ?>

                    <div class="form-group">
                        <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Category & Settings -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Organization</h6>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>
                    
                    <?= $form->field($model, 'category_id')->dropDownList(
                        ['' => 'Select Category...'] + $categories,
                        ['prompt' => 'Select Category...']
                    ) ?>

                    <?= $form->field($model, 'sort_order')->textInput(['type' => 'number', 'min' => 0])->hint('Higher numbers appear first') ?>
                </div>
            </div>

            <!-- Status & Visibility -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>Status & Visibility</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'status')->dropDownList([
                        1 => 'Active',
                        0 => 'Inactive'
                    ]) ?>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="card">
                <div class="card-header">
                    <h6>FAQ Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success"></i> Keep questions clear and concise</li>
                        <li><i class="fas fa-check text-success"></i> Provide complete answers</li>
                        <li><i class="fas fa-check text-success"></i> Use categories to organize related FAQs</li>
                        <li><i class="fas fa-check text-success"></i> Set appropriate sort order</li>
                        <li><i class="fas fa-check text-success"></i> Review for accuracy before publishing</li>
                    </ul>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
