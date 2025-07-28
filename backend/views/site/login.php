<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap5\ActiveForm */
/* @var $model common\models\AdminLoginForm */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Admin Login - FREUDELADEN.DE';
?>
<div class="site-login">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-signin'],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-label fw-semibold'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <div class="mb-3">
        <?= $form->field($model, 'username')->textInput([
            'autofocus' => true,
            'placeholder' => 'Benutzername eingeben'
        ]) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'Passwort eingeben'
        ]) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}",
            'labelOptions' => ['class' => 'form-check-label'],
            'inputOptions' => ['class' => 'form-check-input'],
        ]) ?>
    </div>

    <div class="d-grid">
        <?= Html::submitButton('Anmelden', [
            'class' => 'btn btn-login',
            'name' => 'login-button'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="mt-4 text-center">
        <small class="text-muted">
            <i class="fas fa-shield-alt me-1"></i>
            Sichere Administratoranmeldung
        </small>
    </div>
</div>
