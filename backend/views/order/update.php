<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Order $model */

$this->title = 'Bestellung bearbeiten: ' . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Bestellungen', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_number, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Bearbeiten';
?>
<div class="order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="order-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-6">
                <h4>Bestellstatus</h4>
                
                <?= $form->field($model, 'status')->dropDownList([
                    'pending' => 'Ausstehend',
                    'processing' => 'In Bearbeitung',
                    'shipped' => 'Versandt',
                    'delivered' => 'Geliefert',
                    'cancelled' => 'Storniert',
                ]) ?>

                <?= $form->field($model, 'payment_status')->dropDownList([
                    'pending' => 'Ausstehend',
                    'paid' => 'Bezahlt',
                    'failed' => 'Fehlgeschlagen',
                    'refunded' => 'Rückerstattet',
                ]) ?>

                <?= $form->field($model, 'payment_method')->textInput(['maxlength' => true]) ?>
            </div>
            
            <div class="col-md-6">
                <h4>Kundendaten</h4>
                
                <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'customer_email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'customer_phone')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h4>Rechnungsadresse</h4>
                <?= $form->field($model, 'billing_address')->textarea(['rows' => 4]) ?>
            </div>
            
            <div class="col-md-6">
                <h4>Lieferadresse</h4>
                <?= $form->field($model, 'shipping_address')->textarea(['rows' => 4]) ?>
            </div>
        </div>

        <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>

        <div class="form-group">
            <?= Html::submitButton('Aktualisieren', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Abbrechen', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
