<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Produkte', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Bearbeiten', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Löschen', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sind Sie sicher, dass Sie dieses Produkt löschen möchten?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Zurück zur Liste', ['index'], ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Im Shop anzeigen', ['/site/index', 'r' => 'product/view', 'slug' => $model->slug], [
            'class' => 'btn btn-info',
            'target' => '_blank'
        ]) ?>
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
                    'short_description:ntext',
                    [
                        'attribute' => 'category_id',
                        'value' => $model->category ? $model->category->name : 'Keine Kategorie',
                        'label' => 'Kategorie'
                    ],
                    [
                        'attribute' => 'price',
                        'value' => '€' . number_format($model->price, 2),
                        'label' => 'Preis'
                    ],
                    [
                        'attribute' => 'sale_price',
                        'value' => $model->sale_price ? '€' . number_format($model->sale_price, 2) : 'Nicht gesetzt',
                        'label' => 'Angebotspreis'
                    ],
                    'stock',
                    [
                        'attribute' => 'weight',
                        'value' => $model->weight ? $model->weight . ' kg' : 'Nicht angegeben',
                        'label' => 'Gewicht'
                    ],
                    'dimensions',
                    [
                        'attribute' => 'is_active',
                        'format' => 'boolean',
                        'label' => 'Aktiv'
                    ],
                    [
                        'attribute' => 'is_featured',
                        'format' => 'boolean',
                        'label' => 'Empfohlen'
                    ],
                    'meta_title',
                    'meta_description:ntext',
                    'meta_keywords',
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
                    <p><strong>Varianten:</strong> <?= $model->getProductVariants()->count() ?></p>
                    <p><strong>Bilder:</strong> <?= $model->getProductImages()->count() ?></p>
                    <p><strong>Lagerbestand:</strong> 
                        <span class="badge bg-<?= $model->stock == 0 ? 'danger' : ($model->stock < 10 ? 'warning' : 'success') ?>">
                            <?= $model->stock ?>
                        </span>
                    </p>
                    <?php if ($model->sale_price): ?>
                        <p><strong>Rabatt:</strong> 
                            <?= round((($model->price - $model->sale_price) / $model->price) * 100) ?>%
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($model->getProductVariants()->count() > 0): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Produktvarianten</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <?php foreach ($model->productVariants as $variant): ?>
                                <li class="mb-2">
                                    <strong><?= Html::encode($variant->name) ?></strong><br>
                                    <small class="text-muted">
                                        €<?= number_format($variant->price, 2) ?> | 
                                        Lager: <?= $variant->stock ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
