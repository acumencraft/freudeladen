<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Versand & Lieferung';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">Versand & Lieferung</h1>
                <p class="lead">Alle Informationen zu Versandkosten, Lieferzeiten und Versandarten</p>
            </div>

            <!-- Shipping Overview -->
            <div class="row mb-5">
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                            <h5>Schneller Versand</h5>
                            <p class="text-muted">Versand innerhalb von 24h nach Bestelleingang</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-euro-sign fa-3x text-success mb-3"></i>
                            <h5>Kostenloser Versand</h5>
                            <p class="text-muted">Ab einem Bestellwert von 50€ liefern wir versandkostenfrei</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-truck fa-3x text-info mb-3"></i>
                            <h5>Sichere Verpackung</h5>
                            <p class="text-muted">Ihre Bestellung wird sicher und umweltfreundlich verpackt</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Methods -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-box me-2"></i>Versandarten</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Versandart</th>
                                    <th>Lieferzeit</th>
                                    <th>Versandkosten</th>
                                    <th>Tracking</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>DHL Standard</strong><br>
                                        <small class="text-muted">Standardversand innerhalb Deutschlands</small>
                                    </td>
                                    <td>3-5 Werktage</td>
                                    <td>4,99€<br><small class="text-success">Kostenlos ab 50€</small></td>
                                    <td><i class="fas fa-check text-success"></i> Ja</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>DHL Express</strong><br>
                                        <small class="text-muted">Schneller Versand für eilige Bestellungen</small>
                                    </td>
                                    <td>1-2 Werktage</td>
                                    <td>9,99€</td>
                                    <td><i class="fas fa-check text-success"></i> Ja</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Hermes Standard</strong><br>
                                        <small class="text-muted">Alternative Versandoption</small>
                                    </td>
                                    <td>3-5 Werktage</td>
                                    <td>3,99€<br><small class="text-success">Kostenlos ab 50€</small></td>
                                    <td><i class="fas fa-check text-success"></i> Ja</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Selbstabholung</strong><br>
                                        <small class="text-muted">Abholung in unserem Lager</small>
                                    </td>
                                    <td>Nach Vereinbarung</td>
                                    <td>Kostenlos</td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Zones -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-globe me-2"></i>Versandzonen</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-map-marker-alt text-primary me-2"></i>Deutschland</h5>
                            <ul class="list-unstyled">
                                <li>✓ Kostenloser Versand ab 50€</li>
                                <li>✓ Lieferzeit: 1-3 Werktage</li>
                                <li>✓ Versandkosten: ab 3,99€</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-map-marker-alt text-info me-2"></i>EU-Länder</h5>
                            <ul class="list-unstyled">
                                <li>✓ Kostenloser Versand ab 75€</li>
                                <li>✓ Lieferzeit: 3-7 Werktage</li>
                                <li>✓ Versandkosten: ab 9,99€</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>EU-Länder:</strong> Österreich, Schweiz, Frankreich, Italien, Spanien, Niederlande, Belgien, Luxemburg, Polen, Tschechien, Dänemark, Schweden
                        </small>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-clock me-2"></i>Lieferinformationen</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Bestellabwicklung</h5>
                            <ul>
                                <li>Bestellungen bis 14 Uhr werden am selben Tag verschickt</li>
                                <li>Bestellungen nach 14 Uhr werden am nächsten Werktag verschickt</li>
                                <li>Versand erfolgt Montag bis Freitag</li>
                                <li>Kein Versand an Feiertagen</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Sendungsverfolgung</h5>
                            <ul>
                                <li>Sie erhalten eine Versandbestätigung per E-Mail</li>
                                <li>Tracking-Nummer für die Sendungsverfolgung</li>
                                <li>SMS-Benachrichtigung vor Zustellung (optional)</li>
                                <li>Live-Tracking über unsere Website</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle me-2"></i>Besondere Artikel</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Sperrgut & Große Artikel</h5>
                        <p>Artikel über 30kg oder sperrige Gegenstände werden per Spedition geliefert:</p>
                        <ul class="mb-0">
                            <li>Lieferzeit: 5-10 Werktage</li>
                            <li>Avisierung per Telefon zur Terminvereinbarung</li>
                            <li>Zustellung bis Bordsteinkante oder auf Wunsch ins Haus (gegen Aufpreis)</li>
                            <li>Zusätzliche Versandkosten werden vor Bestellung angezeigt</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contact for Shipping -->
            <div class="card">
                <div class="card-body text-center">
                    <h4>Fragen zum Versand?</h4>
                    <p class="mb-3">Unser Kundenservice hilft Ihnen gerne bei allen Fragen rund um den Versand.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-envelope me-2"></i><strong>E-Mail:</strong> versand@freudeladen.de</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-phone me-2"></i><strong>Hotline:</strong> +49 (0) 123 456789</p>
                        </div>
                    </div>
                    <?= Html::a('Kontakt aufnehmen', ['/site/contact'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

        </div>
    </div>
</div>
