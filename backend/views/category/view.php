<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Category $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kategorien', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Bearbeiten', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Löschen', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sind Sie sicher, dass Sie diese Kategorie löschen möchten?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Zurück zur Liste', ['index'], ['class' => 'btn btn-secondary']) ?>
    </p>

    <div class="row">
        <div class="col-md-8">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'slug',
                    'description:ntext',
                    [
                        'attribute' => 'parent_id',
                        'value' => $model->parent ? $model->parent->name : 'Hauptkategorie',
                        'label' => 'Übergeordnete Kategorie'
                    ],
                    'sort_order',
                    [
                        'attribute' => 'is_active',
                        'format' => 'boolean',
                        'label' => 'Aktiv'
                    ],
                    'meta_title',
                    'meta_description:ntext',
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'label' => 'Erstellt am'
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => 'datetime',
                        'label' => 'Aktualisiert am'
                    ],
                ],
            ]) ?>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Statistiken</h5>
                </div>
                <div class="card-body">
                    <p><strong>Anzahl Produkte:</strong> <?= $model->getProducts()->count() ?></p>
                    <p><strong>Unterkategorien:</strong> <?= $model->getChildren()->count() ?></p>
                    <?php if ($model->parent): ?>
                        <p><strong>Übergeordnet:</strong> <?= Html::a($model->parent->name, ['view', 'id' => $model->parent->id]) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($model->getChildren()->count() > 0): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Unterkategorien</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <?php foreach ($model->children as $child): ?>
                                <li><?= Html::a($child->name, ['view', 'id' => $child->id]) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
