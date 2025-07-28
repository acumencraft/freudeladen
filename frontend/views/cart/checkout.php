<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JqueryAsset;

// Ensure jQuery is loaded
JqueryAsset::register($this);

// Register external CSS and JS files for better SEO and performance
$this->registerCssFile('/css/checkout.css', ['depends' => [\yii\bootstrap5\BootstrapAsset::class]]);
$this->registerJsFile('/js/checkout.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->title = 'Kasse - FREUDELADEN.DE';
$this->params['breadcrumbs'][] = ['label' => 'Warenkorb', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="checkout-page">
    <div class="container mt-4">
        <h1 class="mb-4">
            <i class="fas fa-credit-card me-2"></i>
            Zur Kasse
        </h1>

        <div class="row">
            <div class="col-lg-8">
                <!-- Checkout Form -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Rechnungs- und Lieferadresse</h5>
                    </div>
                    <div class="card-body">
                        <?php $form = ActiveForm::begin([
                            'id' => 'checkout-form',
                            'options' => ['class' => 'needs-validation', 'novalidate' => true]
                        ]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'customer_name')->textInput([
                                    'placeholder' => 'Vor- und Nachname',
                                    'required' => true,
                                    'class' => 'form-control form-control-lg'
                                ])->label('Vollständiger Name *') ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'customer_email')->textInput([
                                    'type' => 'email',
                                    'placeholder' => 'ihre@email.de',
                                    'required' => true,
                                    'class' => 'form-control form-control-lg'
                                ])->label('E-Mail-Adresse *') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'customer_phone')->textInput([
                                    'placeholder' => '+49 123 456789',
                                    'class' => 'form-control form-control-lg'
                                ])->label('Telefonnummer (optional)') ?>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Lieferadresse</h6>
                        <?= $form->field($model, 'shipping_address')->textarea([
                            'rows' => 4,
                            'placeholder' => "Straße und Hausnummer\nPLZ Stadt\nLand",
                            'required' => true,
                            'class' => 'form-control form-control-lg'
                        ])->label('Lieferadresse *') ?>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="same-billing-address" checked>
                            <label class="form-check-label" for="same-billing-address">
                                Rechnungsadresse ist identisch mit Lieferadresse
                            </label>
                        </div>

                        <div id="billing-address-section" style="display: none;">
                            <h6 class="mb-3">Rechnungsadresse</h6>
                            <?= $form->field($model, 'billing_address')->textarea([
                                'rows' => 4,
                                'placeholder' => "Straße und Hausnummer\nPLZ Stadt\nLand",
                                'class' => 'form-control form-control-lg'
                            ])->label('Rechnungsadresse') ?>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Anmerkungen (optional)</h6>
                        <?= $form->field($model, 'notes')->textarea([
                            'rows' => 3,
                            'placeholder' => 'Besondere Wünsche oder Anmerkungen zu Ihrer Bestellung...',
                            'class' => 'form-control'
                        ])->label(false) ?>

                        <hr class="my-4">

                        <!-- Payment Method -->
                        <h6 class="mb-3">Zahlungsart</h6>
                        <div class="payment-methods">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_stripe" value="stripe" checked>
                                <label class="form-check-label" for="payment_stripe">
                                    <i class="far fa-credit-card me-2"></i>
                                    Kreditkarte (Stripe)
                                    <small class="d-block text-muted">Sichere Zahlung mit Visa, MasterCard oder American Express.</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_paypal" value="paypal">
                                <label class="form-check-label" for="payment_paypal">
                                    <i class="fab fa-paypal me-2"></i>
                                    PayPal
                                    <small class="d-block text-muted">Sichere und schnelle Zahlung über PayPal.</small>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                                <label class="form-check-label" for="payment_bank">
                                    <i class="fas fa-university me-2"></i>
                                    Banküberweisung (Vorkasse)
                                    <small class="d-block text-muted">Sie erhalten eine Rechnung mit unseren Bankdaten.</small>
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Terms and Conditions -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="accept-terms" required>
                            <label class="form-check-label" for="accept-terms">
                                Ich habe die <a href="#" target="_blank">Allgemeinen Geschäftsbedingungen</a> 
                                und <a href="#" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere diese. *
                            </label>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Ich möchte den Newsletter abonnieren und über Angebote und Neuigkeiten informiert werden.
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i>
                                Kostenpflichtig bestellen
                            </button>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Ihre Bestellung</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= Html::encode($item->product->name) ?></h6>
                                    <?php if ($item->variant): ?>
                                        <small class="text-muted"><?= Html::encode($item->variant->name) ?></small>
                                    <?php endif; ?>
                                    <div class="text-muted small">Menge: <?= $item->quantity ?></div>
                                </div>
                                <div class="text-end">
                                    <strong>€<?= number_format($item->getPrice() * $item->quantity, 2, ',', '.') ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <hr>

                        <?php 
                        $subtotal = 0;
                        foreach ($cartItems as $item) {
                            $subtotal += $item->getPrice() * $item->quantity;
                        }
                        $tax = round($subtotal * 0.19, 2);
                        $shipping = 5.99;
                        $total = $subtotal + $tax + $shipping;
                        ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Zwischensumme:</span>
                            <span>€<?= number_format($subtotal, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>MwSt. (19%):</span>
                            <span>€<?= number_format($tax, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Versandkosten:</span>
                            <span>€<?= number_format($shipping, 2, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Gesamtsumme:</strong>
                            <strong>€<?= number_format($total, 2, ',', '.') ?></strong>
                        </div>

                        <div class="text-center mt-3">
                            <?= Html::a('Zurück zum Warenkorb', ['index'], [
                                'class' => 'btn btn-outline-secondary'
                            ]) ?>
                        </div>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title">Sicher einkaufen</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <i class="fas fa-lock fa-2x text-success mb-2"></i>
                                <div class="small">SSL-Verschlüsselung</div>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                                <div class="small">Datenschutz</div>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-award fa-2x text-warning mb-2"></i>
                                <div class="small">Trusted Shop</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- External JavaScript and CSS files loaded via registerJsFile() and registerCssFile() -->
