<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Kundenservice';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">Kundenservice</h1>
                <p class="lead">Wir sind für Sie da! Hier finden Sie alle Kontaktmöglichkeiten und häufige Anliegen</p>
            </div>

            <!-- Contact Options -->
            <div class="row mb-5">
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                            <h5>Telefon-Hotline</h5>
                            <p class="text-muted">Mo-Fr: 9:00 - 18:00 Uhr<br>Sa: 10:00 - 16:00 Uhr</p>
                            <a href="tel:+4912345678" class="btn btn-primary">+49 (0) 123 456789</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-envelope fa-3x text-success mb-3"></i>
                            <h5>E-Mail Support</h5>
                            <p class="text-muted">Antwort innerhalb von 24h<br>7 Tage die Woche</p>
                            <a href="mailto:info@freudeladen.de" class="btn btn-success">info@freudeladen.de</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="fas fa-comments fa-3x text-info mb-3"></i>
                            <h5>Live-Chat</h5>
                            <p class="text-muted">Mo-Fr: 9:00 - 18:00 Uhr<br>Sofortige Hilfe</p>
                            <button class="btn btn-info" onclick="openChat()">Chat starten</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Areas -->
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4><i class="fas fa-shopping-cart me-2"></i>Bestellungen & Lieferung</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Bestellstatus abfragen
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Lieferadresse ändern
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Sendungsverfolgung
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Liefertermin verschieben
                                </li>
                            </ul>
                            <div class="mt-3">
                                <?= Html::a('Versandinfos', ['/site/shipping'], ['class' => 'btn btn-outline-primary btn-sm me-2']) ?>
                                <a href="mailto:versand@freudeladen.de" class="btn btn-outline-primary btn-sm">E-Mail</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4><i class="fas fa-undo-alt me-2"></i>Rückgaben & Umtausch</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Rückgabe anmelden
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Umtausch beantragen
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Rückerstattung prüfen
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Retourenstatus verfolgen
                                </li>
                            </ul>
                            <div class="mt-3">
                                <?= Html::a('Rückgabeinfos', ['/site/returns'], ['class' => 'btn btn-outline-primary btn-sm me-2']) ?>
                                <a href="mailto:retouren@freudeladen.de" class="btn btn-outline-primary btn-sm">E-Mail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4><i class="fas fa-credit-card me-2"></i>Zahlungen & Rechnungen</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Zahlungsprobleme lösen
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Rechnung anfordern
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Ratenzahlung beantragen
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Zahlungsmethoden ändern
                                </li>
                            </ul>
                            <div class="mt-3">
                                <a href="mailto:buchhaltung@freudeladen.de" class="btn btn-outline-primary btn-sm">E-Mail</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4><i class="fas fa-user-cog me-2"></i>Kundenkonto & Daten</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Passwort zurücksetzen
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Adresse ändern
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Konto löschen
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Newsletter abbestellen
                                </li>
                            </ul>
                            <div class="mt-3">
                                <a href="mailto:datenschutz@freudeladen.de" class="btn btn-outline-primary btn-sm">E-Mail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="card mb-5">
                <div class="card-header">
                    <h3><i class="fas fa-question-circle me-2"></i>Häufig gestellte Fragen</h3>
                </div>
                <div class="card-body">
                    <p>Viele Antworten finden Sie in unserem FAQ-Bereich. Schauen Sie dort gerne zuerst nach:</p>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="fas fa-box fa-2x text-primary mb-2"></i>
                                <h6>Bestellung & Versand</h6>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="fas fa-undo fa-2x text-success mb-2"></i>
                                <h6>Rückgabe & Umtausch</h6>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="fas fa-credit-card fa-2x text-info mb-2"></i>
                                <h6>Zahlung & Rechnung</h6>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="fas fa-user fa-2x text-warning mb-2"></i>
                                <h6>Kundenkonto</h6>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <?= Html::a('Zur FAQ-Seite', ['/site/faq'], ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>

            <!-- Business Hours -->
            <div class="card mb-5">
                <div class="card-header">
                    <h3><i class="fas fa-clock me-2"></i>Unsere Servicezeiten</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Telefon-Support</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Montag - Freitag:</strong></td>
                                    <td>9:00 - 18:00 Uhr</td>
                                </tr>
                                <tr>
                                    <td><strong>Samstag:</strong></td>
                                    <td>10:00 - 16:00 Uhr</td>
                                </tr>
                                <tr>
                                    <td><strong>Sonntag:</strong></td>
                                    <td>Geschlossen</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>E-Mail & Chat</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>E-Mail:</strong></td>
                                    <td>24/7 (Antwort binnen 24h)</td>
                                </tr>
                                <tr>
                                    <td><strong>Live-Chat:</strong></td>
                                    <td>Mo-Fr: 9:00 - 18:00 Uhr</td>
                                </tr>
                                <tr>
                                    <td><strong>Feiertage:</strong></td>
                                    <td>Eingeschränkter Service</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form Link -->
            <div class="text-center">
                <h3>Haben Sie noch Fragen?</h3>
                <p class="lead">Zögern Sie nicht, uns zu kontaktieren. Wir helfen Ihnen gerne weiter!</p>
                <?= Html::a('Kontaktformular', ['/site/contact'], ['class' => 'btn btn-primary btn-lg me-3']) ?>
                <a href="tel:+4912345678" class="btn btn-success btn-lg">Jetzt anrufen</a>
            </div>

        </div>
    </div>
</div>

<?php
$this->registerJs("
    function openChat() {
        // Hier würde normalerweise ein Chat-Widget geöffnet
        alert('Chat-Funktion wird implementiert. Verwenden Sie bitte in der Zwischenzeit E-Mail oder Telefon.');
    }
");
?>
