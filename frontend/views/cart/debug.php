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

        <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Cart Items Count: <?= count($cartItems) ?><br>
            <?php foreach ($cartItems as $index => $item): ?>
                <div>Item <?= $index + 1 ?>: ID=<?= $item->id ?>, Product ID=<?= $item->product_id ?>, Quantity=<?= $item->quantity ?></div>
                <?php try { ?>
                    <div>- Product exists: <?= $item->product ? 'Yes' : 'No' ?></div>
                    <?php if ($item->product): ?>
                        <div>- Product name: <?= Html::encode($item->product->name) ?></div>
                        <div>- Product price: €<?= number_format($item->product->price, 2) ?></div>
                    <?php endif; ?>
                <?php } catch (Exception $e) { ?>
                    <div class="text-danger">Error accessing product: <?= $e->getMessage() ?></div>
                <?php } ?>
                <hr>
            <?php endforeach; ?>
        </div>

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
            <!-- Simple cart display -->
            <div class="card">
                <div class="card-body">
                    <h5>Cart Items</h5>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="border-bottom p-3">
                            <p><strong>Item ID:</strong> <?= $item->id ?></p>
                            <p><strong>Product ID:</strong> <?= $item->product_id ?></p>
                            <p><strong>Quantity:</strong> <?= $item->quantity ?></p>
                            <button class="btn btn-sm btn-danger remove-item" data-id="<?= $item->id ?>">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="mt-3">
                        <a href="<?= Url::to(['cart/checkout']) ?>" class="btn btn-primary">
                            Zur Kasse
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.remove-item').click(function() {
        const itemId = $(this).data('id');
        console.log('Removing item:', itemId);
        
        if (confirm('Remove this item?')) {
            $.ajax({
                url: '<?= Url::to(['cart/remove']) ?>',
                type: 'POST',
                data: {
                    item_id: itemId,
                    '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
                },
                success: function(response) {
                    console.log('Remove response:', response);
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Error removing item');
                    }
                },
                error: function(xhr) {
                    console.error('Remove error:', xhr.responseText);
                    alert('Error removing item');
                }
            });
        }
    });
});
</script>
