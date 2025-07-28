<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Faq */
/* @var $categories array */

$this->title = 'Update FAQ: ' . substr($model->question, 0, 50) . '...';
$this->params['breadcrumbs'][] = ['label' => 'FAQs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => substr($model->question, 0, 30) . '...', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="faq-update">

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
                        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
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

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h6>FAQ Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label><strong>Created:</strong></label>
                        <p class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
                    </div>
                    
                    <div>
                        <label><strong>Last Updated:</strong></label>
                        <p class="text-muted"><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></p>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
