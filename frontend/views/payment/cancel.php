<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $order common\models\Order */

$this->title = 'Zahlung abgebrochen - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="payment-cancel">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body text-center p-5">
                        <!-- Cancel Icon -->
                        <div class="cancel-icon mb-4">
                            <i class="fas fa-times-circle text-warning" style="font-size: 4rem;"></i>
                        </div>
                        
                        <!-- Cancel Message -->
                        <h1 class="h2 text-warning mb-3">Zahlung abgebrochen</h1>
                        <p class="lead mb-4">
                            Ihre Zahlung wurde abgebrochen. Keine Sorge, es wurden keine Kosten berechnet.
                        </p>
                        
                        <!-- Order Details -->
                        <?php if (isset($order) && $order): ?>
                        <div class="order-details bg-light rounded p-4 mb-4">
                            <h4 class="h5 mb-3">Bestellinformationen</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Bestellnummer:</strong><br>
                                    <span class="text-muted">#<?= Html::encode($order->id) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Betrag:</strong><br>
                                    <span class="h5">€<?= number_format($order->total_amount, 2, ',', '.') ?></span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-warning">Zahlung ausstehend</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Reasons for Cancellation -->
                        <div class="cancellation-reasons bg-light rounded p-4 mb-4">
                            <h4 class="h5 mb-3">Mögliche Gründe für den Abbruch</h4>
                            <div class="row text-start">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user-times text-muted me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Benutzerabbruch</strong><br>
                                            <small class="text-muted">Sie haben die Zahlung selbst abgebrochen.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-credit-card text-muted me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Zahlungsproblem</strong><br>
                                            <small class="text-muted">Problem mit der Zahlungsmethode aufgetreten.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-clock text-muted me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Zeitüberschreitung</strong><br>
                                            <small class="text-muted">Die Zahlungszeit ist abgelaufen.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-wifi text-muted me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Verbindungsproblem</strong><br>
                                            <small class="text-muted">Technisches Problem bei der Übertragung.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Next Steps -->
                        <div class="next-steps mb-4">
                            <h4 class="h5 mb-3">Was können Sie jetzt tun?</h4>
                            <div class="row text-start">
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-redo text-primary me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Erneut versuchen</strong><br>
                                            <small class="text-muted">Starten Sie den Zahlungsvorgang erneut.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exchange-alt text-primary me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Andere Zahlungsart</strong><br>
                                            <small class="text-muted">Wählen Sie eine alternative Zahlungsmethode.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-headset text-primary me-2"></i>
                                        </div>
                                        <div>
                                            <strong>Hilfe kontaktieren</strong><br>
                                            <small class="text-muted">Unser Support-Team hilft Ihnen gerne.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons mb-4">
                            <?php if (isset($order) && $order): ?>
                                <a href="<?= Url::to(['cart/checkout']) ?>" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-redo me-2"></i>
                                    Zahlung wiederholen
                                </a>
                            <?php endif; ?>
                            <a href="<?= Url::to(['cart/index']) ?>" class="btn btn-outline-primary btn-lg me-2">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Warenkorb ansehen
                            </a>
                            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-home me-2"></i>
                                Zur Startseite
                            </a>
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods bg-light rounded p-4 mb-4">
                            <h4 class="h5 mb-3">Verfügbare Zahlungsmethoden</h4>
                            <div class="row">
                                <div class="col-md-4 text-center mb-3">
                                    <i class="far fa-credit-card text-primary mb-2" style="font-size: 2rem;"></i>
                                    <br>
                                    <strong>Kreditkarte</strong>
                                    <br>
                                    <small class="text-muted">Visa, MasterCard, Amex</small>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fab fa-paypal text-primary mb-2" style="font-size: 2rem;"></i>
                                    <br>
                                    <strong>PayPal</strong>
                                    <br>
                                    <small class="text-muted">Schnell und sicher</small>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-university text-primary mb-2" style="font-size: 2rem;"></i>
                                    <br>
                                    <strong>Banküberweisung</strong>
                                    <br>
                                    <small class="text-muted">SEPA-Überweisung</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Support Information -->
                        <div class="support-info mt-4 pt-4 border-top">
                            <p class="mb-1">
                                <strong>Benötigen Sie Hilfe?</strong>
                            </p>
                            <p class="text-muted mb-0">
                                Kontaktieren Sie uns unter <a href="mailto:support@freudeladen.de">support@freudeladen.de</a>
                                oder telefonisch unter <a href="tel:+4989123456789">+49 89 123 456 789</a>
                            </p>
                            <p class="text-muted">
                                <small>Montag bis Freitag: 9:00 - 18:00 Uhr</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-cancel .cancel-icon i {
    animation: warning-pulse 2s infinite;
}

@keyframes warning-pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.05);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.next-steps .fas,
.cancellation-reasons .fas {
    font-size: 1.5rem;
}
</style>
