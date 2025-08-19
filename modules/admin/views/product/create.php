<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Create Product';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="product-form">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Product Information</h3>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                        <?= $form->field($model, 'short_description')->textarea(['rows' => 3]) ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'barcode')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'compare_price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'cost_price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Product Settings</h3>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'status')->dropDownList([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'draft' => 'Draft',
                        ]) ?>

                        <?= $form->field($model, 'category_id')->dropDownList(
                            ['' => 'Select Category...'],
                            ['prompt' => 'Select Category...']
                        ) ?>

                        <?= $form->field($model, 'tags')->textInput(['placeholder' => 'Comma separated tags']) ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'weight')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'stock_quantity')->textInput(['type' => 'number']) ?>
                            </div>
                        </div>

                        <?= $form->field($model, 'track_quantity')->checkbox() ?>

                        <?= $form->field($model, 'featured')->checkbox() ?>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Product Images</h3>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
                        <p class="help-block">Select multiple images for the product.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Save Product', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
