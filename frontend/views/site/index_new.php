<?php

/** @var yii\web\View $this */
/** @var common\models\Product[] $popularProducts */
/** @var common\models\Product[] $saleProducts */
/** @var common\models\Category[] $categories */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Willkommen bei FREUDELADEN.DE';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-light p-5 rounded mb-4">
        <h1 class="display-4">Willkommen bei FREUDELADEN.DE</h1>
        <p class="fs-5 fw-light">Ihre erste Adresse für hochwertige Produkte</p>
        <a class="btn btn-lg btn-dark" href="<?= Url::to(['/product/index']) ?>" role="button">Jetzt einkaufen</a>
    </div>

    <div class="body-content">

        <?php if (!empty($popularProducts)): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4">Beliebte Produkte</h2>
                <div class="row">
                    <?php foreach ($popularProducts as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <?php $mainImage = $product->getMainImage(); ?>
                            <?php if ($mainImage): ?>
                                <img src="<?= Html::encode($mainImage->image_url) ?>" 
                                     class="card-img-top" 
                                     alt="<?= Html::encode($mainImage->alt_text ?: $product->name) ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <span class="text-muted">Kein Bild</span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= Html::encode($product->name) ?></h5>
                                <p class="card-text flex-grow-1"><?= Html::encode($product->short_description) ?></p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if ($product->isOnSale()): ?>
                                                <span class="text-decoration-line-through text-muted">€<?= number_format($product->price, 2) ?></span>
                                                <span class="fw-bold text-danger">€<?= number_format($product->sale_price, 2) ?></span>
                                            <?php else: ?>
                                                <span class="fw-bold">€<?= number_format($product->price, 2) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted"><?= $product->isInStock() ? 'Vorrätig' : 'Ausverkauft' ?></small>
                                    </div>
                                    <div class="mt-2">
                                        <a href="<?= Url::to(['/product/view', 'slug' => $product->slug]) ?>" class="btn btn-outline-dark btn-sm">Details</a>
                                        <?php if ($product->isInStock()): ?>
                                            <button class="btn btn-dark btn-sm add-to-cart" data-product-id="<?= $product->id ?>">In den Warenkorb</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($saleProducts)): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4">Sonderangebote</h2>
                <div class="row">
                    <?php foreach (array_slice($saleProducts, 0, 8) as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <?php $mainImage = $product->getMainImage(); ?>
                            <?php if ($mainImage): ?>
                                <img src="<?= Html::encode($mainImage->image_url) ?>" 
                                     class="card-img-top" 
                                     alt="<?= Html::encode($mainImage->alt_text ?: $product->name) ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <span class="text-muted">Kein Bild</span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= Html::encode($product->name) ?></h5>
                                <p class="card-text flex-grow-1"><?= Html::encode($product->short_description) ?></p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-decoration-line-through text-muted">€<?= number_format($product->price, 2) ?></span>
                                            <span class="fw-bold text-danger">€<?= number_format($product->sale_price, 2) ?></span>
                                            <span class="badge bg-danger ms-1">-<?= $product->getDiscountPercentage() ?>%</span>
                                        </div>
                                        <small class="text-muted"><?= $product->isInStock() ? 'Vorrätig' : 'Ausverkauft' ?></small>
                                    </div>
                                    <div class="mt-2">
                                        <a href="<?= Url::to(['/product/view', 'slug' => $product->slug]) ?>" class="btn btn-outline-dark btn-sm">Details</a>
                                        <?php if ($product->isInStock()): ?>
                                            <button class="btn btn-dark btn-sm add-to-cart" data-product-id="<?= $product->id ?>">In den Warenkorb</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4">
                <h2>Kategorien</h2>
                <p>Durchstöbern Sie unsere Produktkategorien und finden Sie genau das, was Sie suchen.</p>
                <?php if (!empty($categories)): ?>
                    <div class="list-group">
                        <?php foreach (array_slice($categories, 0, 5) as $category): ?>
                            <a href="<?= Url::to(['/product/category', 'slug' => $category->slug]) ?>" 
                               class="list-group-item list-group-item-action">
                                <?= Html::encode($category->name) ?>
                                <small class="text-muted">(<?= count($category->products) ?> Produkte)</small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <h2>Qualität</h2>
                <p>Wir stehen für höchste Qualität und Kundenzufriedenheit. Alle unsere Produkte werden sorgfältig ausgewählt.</p>
                <ul>
                    <li>Hochwertige Materialien</li>
                    <li>Strenge Qualitätskontrolle</li>
                    <li>Nachhaltige Produktion</li>
                    <li>Faire Preise</li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h2>Service</h2>
                <p>Unser Kundenservice steht Ihnen jederzeit zur Verfügung. Kontaktieren Sie uns bei Fragen.</p>
                <ul>
                    <li>Kostenloser Versand ab €50</li>
                    <li>30 Tage Rückgaberecht</li>
                    <li>Sichere Bezahlung</li>
                    <li>Schnelle Lieferung</li>
                </ul>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(function(button) {
        button.addEventListener('click', function() {
            var productId = this.getAttribute('data-product-id');
            
            fetch('<?= Url::to(['/cart/add']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: 'product_id=' + productId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    var cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = data.cartCount;
                    }
                    
                    // Show success message
                    alert('Produkt wurde zum Warenkorb hinzugefügt!');
                } else {
                    alert('Fehler: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ein Fehler ist aufgetreten.');
            });
        });
    });
});
</script>
