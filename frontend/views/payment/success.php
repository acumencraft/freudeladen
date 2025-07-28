<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $order common\models\Order */

$this->title = 'Zahlung erfolgreich - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payment-success">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body text-center p-5">
                        <!-- Success Icon -->
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        
                        <!-- Success Message -->
                        <h1 class="h2 text-success mb-3">Zahlung erfolgreich!</h1>
                        <p class="lead mb-4">
                            Vielen Dank für Ihre Bestellung. Ihre Zahlung wurde erfolgreich verarbeitet.
                        </p>
                        
                        <!-- Order Details -->
                        <div class="order-details bg-light rounded p-4 mb-4">
                            <h4 class="h5 mb-3">Bestelldetails</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Bestellnummer:</strong><br>
                                    <span class="text-primary">#<?= Html::encode($order->id) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Gesamtbetrag:</strong><br>
                                    <span class="h5 text-success">€<?= number_format($order->total_amount, 2, ',', '.') ?></span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Zahlungsart:</strong><br>
                                    <?php
                                    switch ($order->payment_method) {
                                        case 'stripe':
                                            echo '<i class="far fa-credit-card me-1"></i> Kreditkarte';
                                            break;
                                        case 'paypal':
                                            echo '<i class="fab fa-paypal me-1"></i> PayPal';
                                            break;
                                        case 'bank_transfer':
                                            echo '<i class="fas fa-university me-1"></i> Banküberweisung';
                                            break;
                                        default:
                                            echo Html::encode($order->payment_method);
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-success">Bezahlt</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Customer Information -->
                        <div class="customer-info bg-light rounded p-4 mb-4">
                            <h4 class="h5 mb-3">Kundeninformationen</h4>
                            <div class="row text-start">
                                <div class="col-sm-6">
                                    <strong>Name:</strong><br>
                                    <?= Html::encode($order->customer_name) ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>E-Mail:</strong><br>
                                    <?= Html::encode($order->customer_email) ?>
                                </div>
                            </div>
                            <?php if ($order->shipping_address): ?>
                                <hr>
                                <div class="row text-start">
                                    <div class="col-12">
                                        <strong>Lieferadresse:</strong><br>
                                        <?= nl2br(Html::encode($order->shipping_address)) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Next Steps -->
                        <div class="next-steps mb-4">
                            <h4 class="h5 mb-3">Was passiert als nächstes?</h4>
                            <div class="row text-start">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-envelope-circle-check text-primary me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Bestätigung</strong><br>
                                            <small class="text-muted">Sie erhalten eine E-Mail-Bestätigung in wenigen Minuten.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-box text-primary me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Verpackung</strong><br>
                                            <small class="text-muted">Ihre Bestellung wird innerhalb von 1-2 Werktagen verpackt.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-shipping-fast text-primary me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Versand</strong><br>
                                            <small class="text-muted">Lieferung erfolgt in 3-5 Werktagen.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-home me-2"></i>
                                Zur Startseite
                            </a>
                            <a href="<?= Url::to(['cart/index']) ?>" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Weiter einkaufen
                            </a>
                        </div>
                        
                        <!-- Support Information -->
                        <div class="support-info mt-4 pt-4 border-top">
                            <p class="mb-1">
                                <strong>Fragen zu Ihrer Bestellung?</strong>
                            </p>
                            <p class="text-muted mb-0">
                                Kontaktieren Sie uns unter <a href="mailto:support@freudeladen.de">support@freudeladen.de</a>
                                oder telefonisch unter <a href="tel:+4989123456789">+49 89 123 456 789</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-success .success-icon i {
    animation: checkmark 0.6s ease-in-out;
}

@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.next-steps .fas {
    font-size: 1.5rem;
}
</style>
