<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Bestellung bestätigt - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-confirmation">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Success Message -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                        <h1 class="h2 text-success mb-3">Vielen Dank für Ihre Bestellung!</h1>
                        <p class="lead mb-4">
                            Ihre Bestellung wurde erfolgreich aufgegeben und wird schnellstmöglich bearbeitet.
                        </p>
                        <div class="order-number mb-4">
                            <h4>Bestellnummer: <span class="text-primary">#<?= $order->id ?></span></h4>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Bestelldetails</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Kundendaten</h6>
                                <p class="mb-1"><strong><?= Html::encode($order->customer_name) ?></strong></p>
                                <p class="mb-1"><?= Html::encode($order->customer_email) ?></p>
                                <?php if ($order->customer_phone): ?>
                                    <p class="mb-1"><?= Html::encode($order->customer_phone) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Bestelldatum</h6>
                                <p><?= Yii::$app->formatter->asDatetime($order->created_at, 'php:d.m.Y H:i') ?> Uhr</p>
                                
                                <h6 class="mb-3 mt-4">Status</h6>
                                <span class="badge bg-warning">
                                    <?php
                                    $statusLabels = [
                                        'pending' => 'In Bearbeitung',
                                        'confirmed' => 'Bestätigt',
                                        'processing' => 'Wird bearbeitet',
                                        'shipped' => 'Versendet',
                                        'delivered' => 'Zugestellt',
                                        'cancelled' => 'Storniert'
                                    ];
                                    echo $statusLabels[$order->status] ?? $order->status;
                                    ?>
                                </span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Lieferadresse</h6>
                                <address>
                                    <?= nl2br(Html::encode($order->shipping_address)) ?>
                                </address>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Rechnungsadresse</h6>
                                <address>
                                    <?= nl2br(Html::encode($order->billing_address ?: $order->shipping_address)) ?>
                                </address>
                            </div>
                        </div>

                        <?php if ($order->notes): ?>
                            <hr class="my-4">
                            <h6 class="mb-3">Anmerkungen</h6>
                            <p><?= nl2br(Html::encode($order->notes)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Bestellte Artikel</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($order->orderItems as $item): ?>
                            <div class="d-flex align-items-center p-4 border-bottom">
                                <div class="me-3">
                                    <?php 
                                    $imagePath = $item->product->images[0]->image_url ?? null;
                                    $imagePath = $imagePath 
                                        ? '@web/uploads/products/' . $imagePath 
                                        : '@web/images/product-placeholder.jpg';
                                    ?>
                                    <img src="<?= Url::to($imagePath) ?>" 
                                         alt="<?= Html::encode($item->product->name) ?>" 
                                         class="img-fluid rounded" 
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= Html::encode($item->product->name) ?></h6>
                                    <?php if ($item->productVariant): ?>
                                        <small class="text-muted"><?= Html::encode($item->productVariant->name) ?></small>
                                    <?php endif; ?>
                                    <div class="text-muted small">
                                        Artikel-ID: <?= Html::encode($item->product->id) ?>
                                    </div>
                                </div>
                                <div class="text-center me-4">
                                    <div class="text-muted small">Menge</div>
                                    <div class="fw-bold"><?= $item->quantity ?></div>
                                </div>
                                <div class="text-center me-4">
                                    <div class="text-muted small">Einzelpreis</div>
                                    <div>€<?= number_format($item->unit_price, 2, ',', '.') ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small">Gesamtpreis</div>
                                    <div class="fw-bold">€<?= number_format($item->total_price, 2, ',', '.') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Kostenzusammenstellung</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Zwischensumme:</span>
                            <span>€<?= number_format($order->subtotal, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>MwSt. (19%):</span>
                            <span>€<?= number_format($order->tax_amount, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Versandkosten:</span>
                            <span>€<?= number_format($order->shipping_cost, 2, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Gesamtsumme:</strong>
                            <strong class="h5 text-primary">€<?= number_format($order->total_amount, 2, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Wie geht es weiter?</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                                <h6>1. Bestätigung</h6>
                                <p class="text-muted small">Sie erhalten eine Bestätigungs-E-Mail mit allen Bestelldetails.</p>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <i class="fas fa-cogs fa-3x text-info mb-3"></i>
                                <h6>2. Bearbeitung</h6>
                                <p class="text-muted small">Wir bearbeiten Ihre Bestellung und bereiten den Versand vor.</p>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <i class="fas fa-shipping-fast fa-3x text-success mb-3"></i>
                                <h6>3. Versand</h6>
                                <p class="text-muted small">Sie erhalten eine Versandbestätigung mit Tracking-Nummer.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="text-center mb-5">
                    <?= Html::a('Weiter einkaufen', ['site/index'], [
                        'class' => 'btn btn-primary btn-lg me-3'
                    ]) ?>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>
                        Bestellung drucken
                    </button>
                </div>

                <!-- Contact Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title">Fragen zu Ihrer Bestellung?</h6>
                        <p class="card-text">
                            Unser Kundenservice steht Ihnen gerne zur Verfügung.
                        </p>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                                <div class="small">
                                    <strong>Telefon</strong><br>
                                    +49 (0) 123 456789
                                </div>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                <div class="small">
                                    <strong>E-Mail</strong><br>
                                    service@freudeladen.de
                                </div>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                <div class="small">
                                    <strong>Öffnungszeiten</strong><br>
                                    Mo-Fr: 9:00-18:00 Uhr
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .footer, .btn, .breadcrumb {
        display: none !important;
    }
    
    .container {
        max-width: none !important;
        padding: 0 !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
