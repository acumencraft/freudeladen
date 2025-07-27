<?php
/**
 * Category Products View
 * @var yii\web\View $this
 * @var common\models\Category $category
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = $category->name;
$this->params['breadcrumbs'][] = ['label' => 'Produkte', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-products">
    <!-- Category Header -->
    <div class="category-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><?= Html::encode($category->name) ?></h1>
                <?php if ($category->description): ?>
                <p class="lead text-muted"><?= Html::encode($category->description) ?></p>
                <?php endif; ?>
                <p class="text-muted">
                    <i class="fas fa-tag me-2"></i>
                    <?= $dataProvider->getTotalCount() ?> Produkt<?= $dataProvider->getTotalCount() != 1 ? 'e' : '' ?> in dieser Kategorie
                </p>
            </div>
            <div class="col-md-4">
                <!-- Sort Options -->
                <div class="sort-options text-end">
                    <label for="sort-select" class="form-label">Sortieren nach:</label>
                    <select id="sort-select" class="form-select">
                        <option value="name">Name</option>
                        <option value="price_asc">Preis aufsteigend</option>
                        <option value="price_desc">Preis absteigend</option>
                        <option value="newest">Neueste zuerst</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to All Products -->
    <div class="mb-3">
        <a href="<?= Url::to(['product/index']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Alle Produkte
        </a>
    </div>
    
    <!-- Products Grid -->
    <div class="products-grid">
        <?php if ($dataProvider->getCount() > 0): ?>
        <div class="row g-4">
            <?php foreach ($dataProvider->getModels() as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 product-card shadow-sm">
                    <!-- Product Image -->
                    <?php 
                    $primaryImage = $product->images ? $product->images[0] : null;
                    $imageUrl = $primaryImage ? '/uploads/products/' . $primaryImage->filename : '/images/no-image.jpg';
                    ?>
                    <div class="card-img-wrapper">
                        <img src="<?= Html::encode($imageUrl) ?>" 
                             class="card-img-top" 
                             alt="<?= Html::encode($product->name) ?>"
                             style="height: 250px; object-fit: cover;">
                        
                        <!-- Quick Add Button Overlay -->
                        <div class="card-img-overlay d-flex align-items-end justify-content-end p-2">
                            <?php 
                            $defaultVariant = $product->variants ? $product->variants[0] : null;
                            $stock = $defaultVariant ? $defaultVariant->stock_quantity : 0;
                            ?>
                            <?php if ($stock > 0): ?>
                            <button type="button" 
                                    class="btn btn-primary btn-sm quick-add-btn"
                                    data-product-id="<?= $product->id ?>"
                                    data-variant-id="<?= $defaultVariant ? $defaultVariant->id : '' ?>"
                                    data-bs-toggle="tooltip"
                                    title="Schnell hinzufügen">
                                <i class="fas fa-plus"></i>
                            </button>
                            <?php else: ?>
                            <span class="badge bg-danger">Ausverkauft</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="card-body d-flex flex-column">
                        <!-- Product Name -->
                        <h6 class="card-title">
                            <a href="<?= Url::to(['product/view', 'slug' => $product->slug]) ?>" 
                               class="text-decoration-none text-dark">
                                <?= Html::encode($product->name) ?>
                            </a>
                        </h6>
                        
                        <!-- Product Description -->
                        <?php if ($product->description): ?>
                        <p class="card-text text-muted small">
                            <?= Html::encode(mb_substr($product->description, 0, 80) . (mb_strlen($product->description) > 80 ? '...' : '')) ?>
                        </p>
                        <?php endif; ?>
                        
                        <!-- Price and Actions -->
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price">
                                    <?php 
                                    $price = $defaultVariant ? $defaultVariant->price : $product->price;
                                    ?>
                                    <h6 class="text-primary mb-0"><?= number_format($price, 2) ?> €</h6>
                                    <?php if ($product->price != $price): ?>
                                    <small class="text-muted text-decoration-line-through">
                                        <?= number_format($product->price, 2) ?> €
                                    </small>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="actions">
                                    <a href="<?= Url::to(['product/view', 'slug' => $product->slug]) ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper mt-4 d-flex justify-content-center">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'options' => ['class' => 'pagination'],
                'linkOptions' => ['class' => 'page-link'],
                'pageCssClass' => 'page-item',
                'prevPageCssClass' => 'page-item',
                'nextPageCssClass' => 'page-item',
                'firstPageCssClass' => 'page-item',
                'lastPageCssClass' => 'page-item',
                'activePageCssClass' => 'page-item active',
                'disabledPageCssClass' => 'page-item disabled',
            ]) ?>
        </div>
        
        <?php else: ?>
        <!-- No Products Found -->
        <div class="no-products text-center py-5">
            <div class="mb-4">
                <i class="fas fa-box-open fa-4x text-muted"></i>
            </div>
            <h4>Keine Produkte in dieser Kategorie</h4>
            <p class="text-muted">Diese Kategorie enthält derzeit keine Produkte.</p>
            <div class="mt-3">
                <a href="<?= Url::to(['product/index']) ?>" class="btn btn-primary me-2">
                    <i class="fas fa-list me-2"></i>Alle Produkte ansehen
                </a>
                <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Zur Startseite
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.category-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e0e0e0;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.card-img-wrapper {
    position: relative;
    overflow: hidden;
}

.card-img-wrapper .card-img-overlay {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    background: linear-gradient(to top, rgba(0,0,0,0.1), transparent);
}

.card-img-wrapper:hover .card-img-overlay {
    opacity: 1;
}

.quick-add-btn {
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.sort-options .form-select {
    max-width: 200px;
}

@media (max-width: 768px) {
    .category-header {
        text-align: center;
        padding: 20px;
    }
    
    .sort-options {
        text-align: left !important;
        margin-top: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Quick add to cart functionality
    const quickAddBtns = document.querySelectorAll('.quick-add-btn');
    quickAddBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product-id');
            const variantId = this.getAttribute('data-variant-id');
            
            // Quick add with quantity 1
            if (typeof window.addToCart === 'function') {
                window.addToCart(productId, variantId, 1);
            } else {
                console.log('Cart function not available');
            }
        });
    });
    
    // Sort functionality
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('sort', sortValue);
            currentUrl.searchParams.delete('page'); // Reset to first page
            window.location.href = currentUrl.toString();
        });
        
        // Set current sort value from URL
        const urlParams = new URLSearchParams(window.location.search);
        const currentSort = urlParams.get('sort');
        if (currentSort) {
            sortSelect.value = currentSort;
        }
    }
});
</script>
