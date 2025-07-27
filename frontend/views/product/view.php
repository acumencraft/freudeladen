<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $product common\models\Product */
/* @var $relatedProducts common\models\Product[] */

$this->title = $product->name . ' - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = ['label' => 'Produkte', 'url' => ['index']];
if ($product->category) {
    $this->params['breadcrumbs'][] = ['label' => $product->category->name, 'url' => ['category', 'slug' => $product->category->slug]];
}
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="product-view">
    <div class="container mt-4">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6 mb-4">
                <div class="product-images">
                    <?php if ($product->images): ?>
                        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($product->images as $index => $image): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <img src="<?= Html::encode($image->image_url) ?>" 
                                             class="d-block w-100 rounded" 
                                             alt="<?= Html::encode($product->name) ?>"
                                             style="height: 400px; object-fit: cover;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($product->images) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Vorheriges</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Nächstes</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="placeholder-image bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                            <i class="fas fa-image fa-4x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= Url::to(['site/index']) ?>">Start</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?= Url::to(['product/index']) ?>">Produkte</a>
                            </li>
                            <?php if ($product->category): ?>
                                <li class="breadcrumb-item">
                                    <a href="<?= Url::to(['product/category', 'slug' => $product->category->slug]) ?>">
                                        <?= Html::encode($product->category->name) ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="breadcrumb-item active"><?= Html::encode($product->name) ?></li>
                        </ol>
                    </nav>

                    <h1 class="product-title mb-3"><?= Html::encode($product->name) ?></h1>
                    
                    <?php if ($product->category): ?>
                        <div class="product-category mb-3">
                            <span class="badge bg-secondary">
                                <i class="fas fa-tag me-1"></i>
                                <?= Html::encode($product->category->name) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="product-price mb-4">
                        <?php if ($product->sale_price && $product->sale_price < $product->price): ?>
                            <span class="original-price text-muted text-decoration-line-through me-2">
                                €<?= number_format($product->price, 2, ',', '.') ?>
                            </span>
                            <span class="sale-price h3 text-danger">
                                €<?= number_format($product->sale_price, 2, ',', '.') ?>
                            </span>
                            <span class="badge bg-danger ms-2">
                                Sale -<?= round((($product->price - $product->sale_price) / $product->price) * 100) ?>%
                            </span>
                        <?php else: ?>
                            <span class="price h3 text-primary">
                                €<?= number_format($product->getEffectivePrice(), 2, ',', '.') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($product->short_description): ?>
                        <div class="product-short-description mb-4">
                            <p class="lead"><?= Html::encode($product->short_description) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Product Variants -->
                    <?php if ($product->variants): ?>
                        <div class="product-variants mb-4">
                            <h6>Varianten:</h6>
                            <div class="variant-options">
                                <?php foreach ($product->variants as $variant): ?>
                                    <button class="btn btn-outline-secondary me-2 mb-2 variant-btn" 
                                            data-variant-id="<?= $variant->id ?>"
                                            data-price="<?= $variant->price ?>">
                                        <?= Html::encode($variant->name) ?>
                                        <small>(+€<?= number_format($variant->price - $product->price, 2, ',', '.') ?>)</small>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add to Cart Form -->
                    <div class="add-to-cart-section mb-4">
                        <?= Html::beginForm(['cart/add'], 'post', [
                            'id' => 'add-to-cart-form',
                            'class' => 'd-flex align-items-center gap-3'
                        ]) ?>
                        
                        <?= Html::hiddenInput('product_id', $product->id) ?>
                        <?= Html::hiddenInput('variant_id', '', ['id' => 'selected-variant-id']) ?>
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        
                        <div class="quantity-selector">
                            <label for="quantity" class="form-label">Anzahl:</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="1" 
                                   min="1" 
                                   max="99" 
                                   style="width: 80px;">
                        </div>
                        
                        <div class="add-to-cart-btn">
                            <?php if ($product->isInStock()): ?>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    In den Warenkorb
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary btn-lg" disabled>
                                    <i class="fas fa-ban me-2"></i>
                                    Nicht verfügbar
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <?= Html::endForm() ?>
                    </div>

                    <!-- Stock Status -->
                    <div class="stock-status mb-4">
                        <?php if ($product->isInStock()): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Auf Lager (<?= $product->stock ?> verfügbar)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                Nicht auf Lager
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Product Meta -->
                    <div class="product-meta">
                        <ul class="list-unstyled">
                            <li><strong>Produkt-ID:</strong> <?= $product->id ?></li>
                            <?php if (isset($product->weight) && $product->weight): ?>
                                <li><strong>Gewicht:</strong> <?= $product->weight ?>g</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <?php if ($product->description): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Produktbeschreibung</h4>
                        </div>
                        <div class="card-body">
                            <div class="product-description">
                                <?= nl2br(Html::encode($product->description)) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Related Products -->
        <?php if ($relatedProducts): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h4 class="mb-4">Ähnliche Produkte</h4>
                    <div class="row">
                        <?php foreach ($relatedProducts as $relatedProduct): ?>
                            <div class="col-md-3 mb-4">
                                <div class="card h-100">
                                    <?php if ($relatedProduct->images): ?>
                                        <img src="<?= Html::encode($relatedProduct->images[0]->image_url) ?>" 
                                             class="card-img-top" 
                                             alt="<?= Html::encode($relatedProduct->name) ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title"><?= Html::encode($relatedProduct->name) ?></h6>
                                        <p class="card-text text-primary mb-auto">
                                            €<?= number_format($relatedProduct->getEffectivePrice(), 2, ',', '.') ?>
                                        </p>
                                        <a href="<?= Url::to(['view', 'slug' => $relatedProduct->slug]) ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            Details ansehen
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Handle variant selection
document.addEventListener('DOMContentLoaded', function() {
    const variantButtons = document.querySelectorAll('.variant-btn');
    const variantIdInput = document.getElementById('selected-variant-id');
    const priceDisplay = document.querySelector('.price, .sale-price');
    
    variantButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            variantButtons.forEach(btn => btn.classList.remove('btn-secondary', 'active'));
            variantButtons.forEach(btn => btn.classList.add('btn-outline-secondary'));
            
            // Add active class to clicked button
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-secondary', 'active');
            
            // Update hidden input
            const variantId = this.dataset.variantId;
            const variantPrice = this.dataset.price;
            
            if (variantIdInput) {
                variantIdInput.value = variantId;
            }
            
            // Update price display
            if (priceDisplay && variantPrice) {
                priceDisplay.textContent = '€' + parseFloat(variantPrice).toFixed(2).replace('.', ',');
            }
        });
    });

    // Handle add to cart form submission
    const addToCartForm = document.getElementById('add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Produkt wurde zum Warenkorb hinzugefügt!');
                    
                    // Update cart count in navigation if it exists
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement && data.cartCount) {
                        cartCountElement.textContent = data.cartCount;
                    }
                } else {
                    alert('Fehler: ' + (data.message || 'Produkt konnte nicht hinzugefügt werden.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
            });
        });
    }
});
</script>