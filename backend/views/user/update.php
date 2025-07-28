<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Benutzer bearbeiten: ' . $model->email;
$this->params['breadcrumbs'][] = ['label' => 'Benutzer', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->email, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Bearbeiten';
?>

<div class="user-update">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="fas fa-arrow-left me-1"></i>Zurück', ['view', 'id' => $model->id], [
                'class' => 'btn btn-outline-secondary'
            ]) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'status')->dropDownList([
                        1 => 'Aktiv',
                        0 => 'Blockiert'
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'email_verified')->dropDownList([
                        1 => 'Verifiziert',
                        0 => 'Nicht verifiziert'
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'phone_verified')->dropDownList([
                        1 => 'Verifiziert',
                        0 => 'Nicht verifiziert'
                    ]) ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('<i class="fas fa-save me-1"></i>Speichern', [
                    'class' => 'btn btn-success'
                ]) ?>
                <?= Html::a('Abbrechen', ['view', 'id' => $model->id], [
                    'class' => 'btn btn-outline-secondary'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
