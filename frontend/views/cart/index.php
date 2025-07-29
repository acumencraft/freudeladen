<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Warenkorb - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = $this->title;

// Register external cart JavaScript for SEO
$this->registerJsFile('/js/cart-main.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="cart-index">
    <div class="container mt-4">        
        <h1 class="mb-4">
            <i class="fas fa-shopping-cart me-2"></i>
            Ihr Warenkorb
        </h1>

        <?php if (empty($cartItems)): ?>
            <!-- Empty cart -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body py-5">
                            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
                            <h3 class="text-muted mb-3">Ihr Warenkorb ist leer</h3>
                            <p class="text-muted mb-4">Entdecken Sie unsere wunderbaren Produkte und fügen Sie sie zu Ihrem Warenkorb hinzu.</p>
                            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>
                                Weiter einkaufen
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Cart with items -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Artikel im Warenkorb (<?= count($cartItems) ?>)</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($cartItems as $item): ?>
                                <?php 
                                $product = $item->product;
                                $variant = $item->variant;
                                $price = 0;
                                if ($variant && isset($variant->price)) {
                                    $price = $variant->price;
                                } elseif ($product && isset($product->price)) {
                                    $price = $product->price;
                                }
                                ?>
                                <div class="cart-item border-bottom p-4" data-product-id="<?= $item->product_id ?>" data-variant-id="<?= $item->variant_id ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <?php if ($product && $product->images && count($product->images) > 0): ?>
                                                <?php $image = $product->images[0]; ?>
                                                <img src="<?= Html::encode($image->image_url) ?>" alt="<?= Html::encode($product->name) ?>" class="img-fluid rounded">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1"><?= Html::encode($product ? $product->name : 'Unknown Product') ?></h6>
                                            <?php if ($variant): ?>
                                                <small class="text-muted"><?= Html::encode($variant->name) ?></small>
                                            <?php endif; ?>
                                            <br><small class="text-muted">Art.-Nr.: <?= Html::encode($product ? $product->sku : 'N/A') ?></small>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary quantity-decrease" type="button">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control text-center quantity-input" value="<?= $item->quantity ?>" min="1" max="99">
                                                <button class="btn btn-outline-secondary quantity-increase" type="button">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="price">€<?= number_format($price, 2, ',', '.') ?></div>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <div class="subtotal fw-bold">
                                                €<?= number_format($price * $item->quantity, 2, ',', '.') ?>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-1 remove-item" data-id="<?= $item->id ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Bestellübersicht</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Zwischensumme:</span>
                                <span id="cart-subtotal">€<?= number_format($cartTotal, 2, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>MwSt. (19%):</span>
                                <span id="cart-tax">€<?= number_format($cartTotal * 0.19, 2, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Versandkosten:</span>
                                <span>€5,99</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Gesamtsumme:</strong>
                                <strong id="cart-total">€<?= number_format($cartTotal + ($cartTotal * 0.19) + 5.99, 2, ',', '.') ?></strong>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="<?= Url::to(['cart/checkout']) ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-lock me-2"></i>
                                    Zur Kasse
                                </a>
                                <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Weiter einkaufen
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trust badges -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body text-center">
                            <div class="row text-center">
                                <div class="col-4">
                                    <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                                    <div class="small">Schneller Versand</div>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                    <div class="small">Sicher bezahlen</div>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-undo fa-2x text-info mb-2"></i>
                                    <div class="small">14 Tage Rückgabe</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
