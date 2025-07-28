<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $order common\models\Order */

$this->title = 'Überweisungsdetails - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bank-instructions">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-5">
                            <div class="bank-icon mb-3">
                                <i class="fas fa-university text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h1 class="h2 text-primary mb-3">Banküberweisung</h1>
                            <p class="lead">
                                Bitte überweisen Sie den Betrag auf das unten angegebene Konto.
                            </p>
                        </div>
                        
                        <!-- Order Information -->
                        <div class="order-info bg-primary text-white rounded p-4 mb-5">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h4 class="h5 mb-2">Bestellnummer</h4>
                                    <p class="h3 mb-0">#<?= Html::encode($order->id) ?></p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <h4 class="h5 mb-2">Überweisungsbetrag</h4>
                                    <p class="h2 mb-0">€<?= number_format($order->total_amount, 2, ',', '.') ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bank Details -->
                        <div class="bank-details mb-5">
                            <h3 class="h4 mb-4">
                                <i class="fas fa-building me-2 text-primary"></i>
                                Bankverbindung
                            </h3>
                            
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="bank-detail-card bg-light rounded p-4 h-100">
                                        <h5 class="mb-3">Empfänger</h5>
                                        <div class="bank-field mb-3">
                                            <label class="text-muted small">Kontoinhaber:</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>FREUDELADEN GmbH</strong>
                                                <button class="btn btn-sm btn-outline-primary copy-btn" 
                                                        onclick="copyToClipboard('FREUDELADEN GmbH')" 
                                                        title="Kopieren">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="bank-field mb-3">
                                            <label class="text-muted small">Bank:</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>Deutsche Bank AG</strong>
                                                <button class="btn btn-sm btn-outline-primary copy-btn" 
                                                        onclick="copyToClipboard('Deutsche Bank AG')" 
                                                        title="Kopieren">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="bank-field">
                                            <label class="text-muted small">BIC/SWIFT:</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>DEUTDEFF</strong>
                                                <button class="btn btn-sm btn-outline-primary copy-btn" 
                                                        onclick="copyToClipboard('DEUTDEFF')" 
                                                        title="Kopieren">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 mb-4">
                                    <div class="bank-detail-card bg-light rounded p-4 h-100">
                                        <h5 class="mb-3">Kontodaten</h5>
                                        <div class="bank-field mb-3">
                                            <label class="text-muted small">IBAN:</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>DE89 3704 0044 0532 0130 00</strong>
                                                <button class="btn btn-sm btn-outline-primary copy-btn" 
                                                        onclick="copyToClipboard('DE89370400440532013000')" 
                                                        title="Kopieren">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="bank-field mb-3">
                                            <label class="text-muted small">Betrag:</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>€<?= number_format($order->total_amount, 2, ',', '.') ?></strong>
                                                <button class="btn btn-sm btn-outline-primary copy-btn" 
                                                        onclick="copyToClipboard('<?= number_format($order->total_amount, 2, ',', '.') ?>')" 
                                                        title="Kopieren">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="bank-field">
                                            <label class="text-muted small">Verwendungszweck:</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>Bestellung #<?= Html::encode($order->id) ?></strong>
                                                <button class="btn btn-sm btn-outline-primary copy-btn" 
                                                        onclick="copyToClipboard('Bestellung #<?= Html::encode($order->id) ?>')" 
                                                        title="Kopieren">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Important Notes -->
                        <div class="important-notes mb-5">
                            <h3 class="h4 mb-4">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                Wichtige Hinweise
                            </h3>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="note-item d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-clock text-primary me-3"></i>
                                        </div>
                                        <div>
                                            <strong>Überweisungsfrist</strong><br>
                                            <small class="text-muted">
                                                Bitte überweisen Sie den Betrag innerhalb von 7 Tagen. 
                                                Bei Überschreitung wird die Bestellung automatisch storniert.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="note-item d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-edit text-primary me-3"></i>
                                        </div>
                                        <div>
                                            <strong>Verwendungszweck</strong><br>
                                            <small class="text-muted">
                                                Geben Sie unbedingt die Bestellnummer #<?= Html::encode($order->id) ?> 
                                                als Verwendungszweck an.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="note-item d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-euro-sign text-primary me-3"></i>
                                        </div>
                                        <div>
                                            <strong>Exakter Betrag</strong><br>
                                            <small class="text-muted">
                                                Überweisen Sie bitte den exakten Betrag von 
                                                €<?= number_format($order->total_amount, 2, ',', '.') ?>.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="note-item d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-shipping-fast text-primary me-3"></i>
                                        </div>
                                        <div>
                                            <strong>Versand</strong><br>
                                            <small class="text-muted">
                                                Der Versand erfolgt nach Zahlungseingang 
                                                innerhalb von 1-2 Werktagen.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="order-summary bg-light rounded p-4 mb-5">
                            <h3 class="h4 mb-4">Bestellübersicht</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Kunde:</strong><br>
                                    <?= Html::encode($order->customer_name) ?><br>
                                    <?= Html::encode($order->customer_email) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Lieferadresse:</strong><br>
                                    <?= nl2br(Html::encode($order->shipping_address)) ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons text-center mb-4">
                            <button class="btn btn-primary btn-lg me-3" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>
                                Überweisungsdetails drucken
                            </button>
                            <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-home me-2"></i>
                                Zur Startseite
                            </a>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="contact-info text-center pt-4 border-top">
                            <p class="mb-1">
                                <strong>Fragen zur Überweisung?</strong>
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
@media print {
    .action-buttons {
        display: none !important;
    }
    
    .container {
        max-width: 100% !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}

.bank-detail-card {
    transition: all 0.3s ease;
}

.bank-detail-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.copy-btn {
    transition: all 0.2s ease;
}

.copy-btn:hover {
    transform: scale(1.1);
}

.note-item {
    margin-bottom: 1rem;
}

.note-item i {
    font-size: 1.2rem;
    margin-top: 0.2rem;
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback
        const button = event.target.closest('.copy-btn');
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-success"></i>';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-primary');
        
        setTimeout(function() {
            button.innerHTML = originalHtml;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
    });
}
</script>
