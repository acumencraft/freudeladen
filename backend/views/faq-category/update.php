<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FaqCategory */

$this->title = 'Update FAQ Category: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'FAQ Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="faq-category-update">

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

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'description')->textarea(['rows' => 4])->hint('Optional description for this category') ?>

                    <div class="form-group">
                        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
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

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h6>Category Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center">
                                <h4 class="text-primary"><?= $model->getFaqCount() ?></h4>
                                <p class="text-muted small">Total FAQs</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <h4 class="text-success"><?= $model->getActiveFaqCount() ?></h4>
                                <p class="text-muted small">Active FAQs</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label><strong>Created:</strong></label>
                        <p class="text-muted small"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></p>
                    </div>
                    
                    <div>
                        <label><strong>Last Updated:</strong></label>
                        <p class="text-muted small"><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></p>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
