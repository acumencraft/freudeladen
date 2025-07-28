<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ShippingMethod */

$this->title = 'Create Shipping Method';
$this->params['breadcrumbs'][] = ['label' => 'Shipping Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shipping-method-create">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
                </div>
                <div class="card-body">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

</div>
