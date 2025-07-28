<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BlogCategory */

$this->title = 'Update Blog Category: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Blog Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="blog-category-update">

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

                    <div class="form-group">
                        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6>Category Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label><strong>Total Posts:</strong></label>
                        <span class="badge badge-info"><?= $model->getPostCount() ?></span>
                    </div>
                    
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
        </div>
    </div>

</div>
