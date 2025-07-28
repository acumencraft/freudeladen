<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Warenkorb - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = $this->title;

// Remove the external JS file registration for now
// $this->registerJsFile('@web/js/cart.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="cart-index">
    <div class="container mt-4">
        <h1 class="mb-4">
            <i class="fas fa-shopping-cart me-2"></i>
            Ihr Warenkorb
        </h1>

        <?php if (empty($cartItems)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted mb-3">Ihr Warenkorb ist leer</h4>
                            <p class="text-muted mb-4">Entdecken Sie unsere hochwertigen Produkte und füllen Sie Ihren Warenkorb.</p>
                            <?= Html::a('Weiter einkaufen', ['site/index'], [
                                'class' => 'btn btn-primary btn-lg'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Artikel im Warenkorb</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($cartItems as $index => $item): ?>
                                <div class="cart-item border-bottom p-4" data-product-id="<?= $item->product_id ?>" 
                                     data-variant-id="<?= $item->variant_id ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 100px;">
                                                <span class="text-muted">Bild</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1"><?= Html::encode($item->product->name) ?></h6>
                                            <?php if ($item->variant): ?>
                                                <small class="text-muted"><?= Html::encode($item->variant->name) ?></small>
                                            <?php endif; ?>
                                            <div class="text-muted small mt-1">
                                                Artikel-ID: <?= Html::encode($item->product->id) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm">
                                                <button type="button" class="btn btn-outline-secondary quantity-decrease">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control text-center quantity-input" 
                                                       value="<?= $item->quantity ?>" min="1" max="99">
                                                <button type="button" class="btn btn-outline-secondary quantity-increase">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <?php $price = $item->variant ? $item->variant->price : $item->product->price; ?>
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
                            <?php 
                            $subtotal = 0;
                            foreach ($cartItems as $item) {
                                $subtotal += $item['price'] * $item['quantity'];
                            }
                            $tax = round($subtotal * 0.19, 2);
                            $shipping = 5.99;
                            $total = $subtotal + $tax + $shipping;
                            ?>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Zwischensumme:</span>
                                <span id="cart-subtotal">€<?= number_format($subtotal, 2, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>MwSt. (19%):</span>
                                <span id="cart-tax">€<?= number_format($tax, 2, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Versandkosten:</span>
                                <span>€<?= number_format($shipping, 2, ',', '.') ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Gesamtsumme:</strong>
                                <strong id="cart-total">€<?= number_format($total, 2, ',', '.') ?></strong>
                            </div>

                            <div class="d-grid gap-2">
                                <?= Html::a('Zur Kasse', ['cart/checkout'], [
                                    'class' => 'btn btn-primary btn-lg'
                                ]) ?>
                                <?= Html::a('Weiter einkaufen', ['site/index'], [
                                    'class' => 'btn btn-outline-secondary'
                                ]) ?>
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100" id="clear-cart">
                                    <i class="fas fa-trash me-1"></i>
                                    Warenkorb leeren
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Trust badges -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-body text-center">
                            <h6 class="card-title">Vertrauen Sie uns</h6>
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

<script>
// Update cart totals
function updateCartTotals() {
    let subtotal = 0;
    $('.cart-item').each(function() {
        const price = parseFloat($(this).find('.price').text().replace('€', '').replace(',', '.'));
        const quantity = parseInt($(this).find('.quantity-input').val());
        const itemTotal = price * quantity;
        
        $(this).find('.subtotal').text('€' + itemTotal.toFixed(2).replace('.', ','));
        subtotal += itemTotal;
    });
    
    const tax = subtotal * 0.19;
    const shipping = 5.99;
    const total = subtotal + tax + shipping;
    
    $('#cart-subtotal').text('€' + subtotal.toFixed(2).replace('.', ','));
    $('#cart-tax').text('€' + tax.toFixed(2).replace('.', ','));
    $('#cart-total').text('€' + total.toFixed(2).replace('.', ','));
}

$(document).ready(function() {
    // Quantity increase/decrease
    $('.quantity-increase').click(function() {
        const input = $(this).siblings('.quantity-input');
        const current = parseInt(input.val());
        if (current < 99) {
            input.val(current + 1);
            updateQuantity($(this).closest('.cart-item'));
        }
    });
    
    $('.quantity-decrease').click(function() {
        const input = $(this).siblings('.quantity-input');
        const current = parseInt(input.val());
        if (current > 1) {
            input.val(current - 1);
            updateQuantity($(this).closest('.cart-item'));
        }
    });
    
    $('.quantity-input').change(function() {
        updateQuantity($(this).closest('.cart-item'));
    });
    
    // Remove item
    $('.remove-item').click(function() {
        console.log('Remove button clicked'); // DEBUG
        const itemId = $(this).data('id');
        console.log('Item ID:', itemId); // DEBUG
        
        if (confirm('Sind Sie sicher, dass Sie diesen Artikel entfernen möchten?')) {
            removeFromCart($(this));
        }
    });
});    // Clear cart
    $('#clear-cart').click(function() {
        if (confirm('Möchten Sie wirklich alle Artikel aus dem Warenkorb entfernen?')) {
            clearCart();
        }
    });
});

function updateQuantity(cartItem) {
    const productId = cartItem.data('product-id');
    const variantId = cartItem.data('variant-id');
    const quantity = cartItem.find('.quantity-input').val();
    
    $.ajax({
        url: '<?= Url::to(['cart/update']) ?>',
        type: 'POST',
        data: {
            product_id: productId,
            variant_id: variantId,
            quantity: quantity,
            '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
        },
        success: function(response) {
            if (response.success) {
                updateCartTotals();
                updateCartCounter();
            } else {
                alert(response.message || 'Fehler beim Aktualisieren der Menge');
            }
        },
        error: function() {
            alert('Fehler beim Aktualisieren der Menge');
        }
    });
}

function removeFromCart(removeButton) {
    const itemId = removeButton.data('id');
    
    $.ajax({
        url: '<?= Url::to(['cart/remove']) ?>',
        type: 'POST',
        data: {
            item_id: itemId,
            '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
        },
        success: function(response) {
            if (response.success) {
                removeButton.closest('.cart-item').fadeOut(300, function() {
                    $(this).remove();
                    updateCartTotals();
                    updateCartCounter();
                    
                    // Check if cart is empty
                    if ($('.cart-item').length === 0) {
                        location.reload();
                    }
                });
            } else {
                alert(response.message || 'Fehler beim Entfernen des Artikels');
            }
        },
        error: function() {
            alert('Fehler beim Entfernen des Artikels');
        }
    });
}

function clearCart() {
    $.ajax({
        url: '<?= Url::to(['cart/clear']) ?>',
        type: 'POST',
        data: {
            '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Fehler beim Leeren des Warenkorbs');
            }
        },
        error: function() {
            alert('Fehler beim Leeren des Warenkorbs');
        }
    });
}

function updateCartCounter() {
    $.ajax({
        url: '<?= Url::to(['cart/count']) ?>',
        type: 'GET',
        success: function(response) {
            $('#cart-count').text(response.cartCount);
            if (response.cartCount > 0) {
                $('#cart-count').show();
            } else {
                $('#cart-count').hide();
            }
        }
    });
}
</script>
