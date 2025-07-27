<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Warenkorb - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="cart-index">
    <div class="container mt-4">
        <h1 class="mb-4">
            <i class="fas fa-shopping-cart me-2"></i>
            Ihr Warenkorb
        </h1>

        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                <h4>Ihr Warenkorb ist leer</h4>
                <p>Entdecken Sie unsere hochwertigen Produkte.</p>
                <?= Html::a('Weiter einkaufen', ['site/index'], ['class' => 'btn btn-primary']) ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <h3>Warenkorb Inhalt (<?= count($cartItems) ?> Artikel)</h3>
                    
                    <?php foreach ($cartItems as $item): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><?= Html::encode($item->product->name) ?></h5>
                                    <p class="text-muted">Artikel-ID: <?= Html::encode($item->product->id) ?></p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Menge: <?= $item->quantity ?></strong>
                                </div>
                                <div class="col-md-2">
                                    <?php $price = $item->variant ? $item->variant->price : $item->product->price; ?>
                                    <strong>€<?= number_format($price, 2) ?></strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Total: €<?= number_format($price * $item->quantity, 2) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="mt-4">
                        <h4>Gesamtsumme: €<?= number_format($cartTotal, 2) ?></h4>
                        <div class="mt-3">
                            <?= Html::a('Weiter einkaufen', ['site/index'], ['class' => 'btn btn-secondary me-2']) ?>
                            <?= Html::a('Zur Kasse', ['cart/checkout'], ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
