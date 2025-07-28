<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SeoPage */

$this->title = 'Update SEO Page: ' . $model->route;
$this->params['breadcrumbs'][] = ['label' => 'SEO Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'SEO Pages', 'url' => ['pages']];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="seo-page-update">

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Updating SEO settings for: <strong><?= Html::encode($model->route) ?></strong>
                <?php if ($model->title): ?>
                    - <?= Html::encode($model->title) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'isUpdate' => true,
    ]) ?>

</div>
