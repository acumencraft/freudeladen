<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Rückgabe & Umtausch';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">Rückgabe & Umtausch</h1>
                <p class="lead">Ihre Zufriedenheit ist uns wichtig - hier finden Sie alle Informationen zu Rückgaben</p>
            </div>

            <!-- Return Policy Overview -->
            <div class="row mb-5">
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                            <h5>30 Tage Rückgaberecht</h5>
                            <p class="text-muted">Kostenlose Rückgabe innerhalb von 30 Tagen nach Erhalt</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-undo-alt fa-3x text-success mb-3"></i>
                            <h5>Einfache Abwicklung</h5>
                            <p class="text-muted">Unkomplizierte Rückgabe über unser Online-Portal</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave fa-3x text-info mb-3"></i>
                            <h5>Schnelle Erstattung</h5>
                            <p class="text-muted">Rückerstattung innerhalb von 14 Tagen nach Erhalt der Rückware</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Return Process -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-list-ol me-2"></i>So funktioniert die Rückgabe</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">1</div>
                            <h5 class="mt-3">Rückgabe anmelden</h5>
                            <p class="text-muted">Melden Sie Ihre Rückgabe in Ihrem Kundenkonto oder per E-Mail an</p>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">2</div>
                            <h5 class="mt-3">Paket vorbereiten</h5>
                            <p class="text-muted">Verpacken Sie die Artikel in der Originalverpackung und legen Sie den Rückgabeschein bei</p>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">3</div>
                            <h5 class="mt-3">Paket versenden</h5>
                            <p class="text-muted">Verwenden Sie das beiliegende Retourenlabel oder frankieren Sie das Paket selbst</p>
                        </div>
                        <div class="col-md-3 text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">4</div>
                            <h5 class="mt-3">Erstattung erhalten</h5>
                            <p class="text-muted">Nach Prüfung erhalten Sie die Rückerstattung auf Ihr ursprüngliches Zahlungsmittel</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Return Conditions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-check-circle me-2"></i>Rückgabebedingungen</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-success"><i class="fas fa-check me-2"></i>Das können Sie zurückgeben:</h5>
                            <ul>
                                <li>Unbenutzte Artikel in Originalverpackung</li>
                                <li>Artikel mit allen Etiketten und Aufklebern</li>
                                <li>Artikel ohne Beschädigungen</li>
                                <li>Artikel innerhalb der 30-Tage-Frist</li>
                                <li>Artikel mit Originalrechnung oder Lieferschein</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-danger"><i class="fas fa-times me-2"></i>Das können wir nicht zurücknehmen:</h5>
                            <ul>
                                <li>Personalisierte oder maßgefertigte Artikel</li>
                                <li>Hygieneartikel (aus Hygienegründen)</li>
                                <li>Artikel mit Sonderanfertigungen</li>
                                <li>Artikel nach Ablauf der 30-Tage-Frist</li>
                                <li>Stark beschädigte oder verschmutzte Artikel</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Return Costs -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-euro-sign me-2"></i>Rückgabekosten</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Grund der Rückgabe</th>
                                    <th>Rücksendekosten</th>
                                    <th>Hinweis</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Widerruf / Gefällt nicht</strong></td>
                                    <td class="text-success">Kostenlos*</td>
                                    <td>Bei Nutzung des beiliegenden Retourenlabels</td>
                                </tr>
                                <tr>
                                    <td><strong>Falsche Größe / Farbe</strong></td>
                                    <td class="text-success">Kostenlos</td>
                                    <td>Umtausch gegen richtige Größe/Farbe</td>
                                </tr>
                                <tr>
                                    <td><strong>Defekter / Beschädigter Artikel</strong></td>
                                    <td class="text-success">Kostenlos</td>
                                    <td>Wir übernehmen alle Kosten</td>
                                </tr>
                                <tr>
                                    <td><strong>Falsche Lieferung</strong></td>
                                    <td class="text-success">Kostenlos</td>
                                    <td>Unser Fehler - wir tragen alle Kosten</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">*Retourenlabel liegt jeder Sendung bei</small>
                </div>
            </div>

            <!-- Refund Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-credit-card me-2"></i>Rückerstattung</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Erstattungsmethoden</h5>
                            <ul>
                                <li><strong>Kreditkarte:</strong> 3-5 Werktage</li>
                                <li><strong>PayPal:</strong> 1-3 Werktage</li>
                                <li><strong>Banküberweisung:</strong> 3-7 Werktage</li>
                                <li><strong>SEPA-Lastschrift:</strong> 3-5 Werktage</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Erstattungsbeträge</h5>
                            <ul>
                                <li>Vollständige Erstattung des Kaufpreises</li>
                                <li>Rückerstattung der ursprünglichen Versandkosten*</li>
                                <li>Keine versteckten Gebühren</li>
                                <li>Automatische Bearbeitung nach Wareneingang</li>
                            </ul>
                            <small class="text-muted">*Nur bei Widerruf der gesamten Bestellung</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exchange Service -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-exchange-alt me-2"></i>Umtauschservice</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Schneller Umtausch</h5>
                        <p>Möchten Sie einen Artikel gegen eine andere Größe oder Farbe umtauschen?</p>
                        <ul class="mb-3">
                            <li>Bestellen Sie den gewünschten Artikel neu</li>
                            <li>Senden Sie den alten Artikel zurück</li>
                            <li>Nach Erhalt erstatten wir den Kaufpreis</li>
                            <li>So haben Sie den neuen Artikel schneller</li>
                        </ul>
                        <?= Html::a('Neuen Artikel bestellen', ['/product/index'], ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>

            <!-- Return Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-map-marker-alt me-2"></i>Rücksendeadresse</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Retouren-Zentrum</h5>
                            <address>
                                <strong>FREUDELADEN.DE - Retouren</strong><br>
                                Retourenstraße 123<br>
                                12345 Musterhausen<br>
                                Deutschland
                            </address>
                        </div>
                        <div class="col-md-6">
                            <h5>Wichtige Hinweise</h5>
                            <ul>
                                <li>Verwenden Sie immer das Retourenlabel</li>
                                <li>Verpacken Sie Artikel sicher</li>
                                <li>Legen Sie den Rückgabeschein bei</li>
                                <li>Notieren Sie sich die Sendungsnummer</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact for Returns -->
            <div class="card">
                <div class="card-body text-center">
                    <h4>Fragen zur Rückgabe?</h4>
                    <p class="mb-3">Unser Kundenservice hilft Ihnen gerne bei allen Fragen rund um Rückgaben und Umtausch.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-envelope me-2"></i><strong>E-Mail:</strong> retouren@freudeladen.de</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-phone me-2"></i><strong>Hotline:</strong> +49 (0) 123 456789</p>
                        </div>
                    </div>
                    <?= Html::a('Rückgabe anmelden', ['/site/contact'], ['class' => 'btn btn-primary me-2']) ?>
                    <?= Html::a('Kontakt aufnehmen', ['/site/contact'], ['class' => 'btn btn-outline-primary']) ?>
                </div>
            </div>

        </div>
    </div>
</div>
