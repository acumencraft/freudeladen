<?php
/**
 * Product Index (All Products)
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\Category[] $categories
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap5\Nav;

$this->title = 'Alle Produkte';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="product-index">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>
            <p class="text-muted">Entdecken Sie unser komplettes Sortiment</p>
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
    
    <!-- Category Filter -->
    <?php if ($categories): ?>
    <div class="category-filter mb-4">
        <h5>Kategorien:</h5>
        <?php
        echo Nav::widget([
            'options' => ['class' => 'nav nav-pills flex-wrap'],
            'items' => array_merge(
                [['label' => 'Alle', 'url' => ['product/index'], 'active' => !Yii::$app->request->get('category')]],
                array_map(function($category) {
                    return [
                        'label' => $category->name . ' (' . count($category->products) . ')',
                        'url' => ['product/category', 'slug' => $category->slug],
                        'active' => Yii::$app->request->get('category') == $category->slug
                    ];
                }, $categories)
            ),
        ]);
        ?>
    </div>
    <?php endif; ?>
    
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
                        <!-- Category -->
                        <?php if ($product->category): ?>
                        <small class="text-muted mb-1">
                            <a href="<?= Url::to(['product/category', 'slug' => $product->category->slug]) ?>" 
                               class="text-decoration-none text-muted">
                                <?= Html::encode($product->category->name) ?>
                            </a>
                        </small>
                        <?php endif; ?>
                        
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
            <h4>Keine Produkte gefunden</h4>
            <p class="text-muted">Es wurden keine Produkte in dieser Kategorie gefunden.</p>
            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Zur Startseite
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
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

.nav-pills .nav-link {
    border-radius: 25px;
    margin-right: 10px;
    margin-bottom: 10px;
}

.sort-options .form-select {
    max-width: 200px;
}

@media (max-width: 768px) {
    .sort-options {
        text-align: left !important;
        margin-top: 20px;
    }
    
    .category-filter .nav {
        justify-content: center;
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
