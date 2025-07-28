<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $faqs common\models\Faq[] */
/* @var $categories common\models\FaqCategory[] */

$this->title = 'FAQ - Häufig gestellte Fragen';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">Häufig gestellte Fragen</h1>
                <p class="lead">Hier finden Sie Antworten auf die häufigsten Fragen zu unserem Online-Shop</p>
            </div>

            <!-- FAQ Categories Navigation -->
            <?php if (!empty($categories)): ?>
            <div class="mb-4">
                <nav class="nav nav-pills nav-fill">
                    <a class="nav-link active" href="#all" data-bs-toggle="pill">Alle</a>
                    <?php foreach ($categories as $category): ?>
                        <a class="nav-link" href="#category-<?= $category->id ?>" data-bs-toggle="pill">
                            <?= Html::encode($category->name) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
            <?php endif; ?>

            <!-- FAQ Accordion -->
            <div class="accordion" id="faqAccordion">
                <?php if (!empty($faqs)): ?>
                    <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item faq-item" data-category="<?= $faq->category_id ?>">
                        <h2 class="accordion-header" id="heading<?= $faq->id ?>">
                            <button class="accordion-button<?= $index === 0 ? '' : ' collapsed' ?>" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse<?= $faq->id ?>" 
                                    aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" 
                                    aria-controls="collapse<?= $faq->id ?>">
                                <i class="fas fa-question-circle me-2"></i>
                                <?= Html::encode($faq->question) ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $faq->id ?>" 
                             class="accordion-collapse collapse<?= $index === 0 ? ' show' : '' ?>" 
                             aria-labelledby="heading<?= $faq->id ?>" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?= nl2br(Html::encode($faq->answer)) ?>
                                
                                <?php if ($faq->category): ?>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-tag"></i> 
                                        Kategorie: <?= Html::encode($faq->category->name) ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <h3>Keine FAQs verfügbar</h3>
                        <p class="text-muted">Derzeit sind keine häufig gestellten Fragen verfügbar.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contact Section -->
            <div class="card mt-5">
                <div class="card-body text-center">
                    <h4>Ihre Frage ist nicht dabei?</h4>
                    <p class="mb-3">Unser Kundenservice hilft Ihnen gerne weiter!</p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-envelope fa-2x text-primary me-3"></i>
                                <div>
                                    <strong>E-Mail</strong><br>
                                    <a href="mailto:info@freudeladen.de">info@freudeladen.de</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-phone fa-2x text-primary me-3"></i>
                                <div>
                                    <strong>Telefon</strong><br>
                                    <a href="tel:+4912345678">+49 (0) 123 456789</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock fa-2x text-primary me-3"></i>
                                <div>
                                    <strong>Öffnungszeiten</strong><br>
                                    Mo-Fr: 9-18 Uhr
                                </div>
                            </div>
                        </div>
                    </div>
                    <?= Html::a('Kontakt aufnehmen', ['/site/contact'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    // FAQ Category Filter
    $('[data-bs-toggle=\"pill\"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr('href');
        
        if (target === '#all') {
            $('.faq-item').show();
        } else {
            var categoryId = target.replace('#category-', '');
            $('.faq-item').hide();
            $('.faq-item[data-category=\"' + categoryId + '\"]').show();
        }
    });
    
    // Search functionality (optional enhancement)
    function filterFAQs(searchTerm) {
        $('.faq-item').each(function() {
            var question = $(this).find('.accordion-button').text().toLowerCase();
            var answer = $(this).find('.accordion-body').text().toLowerCase();
            
            if (question.includes(searchTerm.toLowerCase()) || answer.includes(searchTerm.toLowerCase())) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
");
?>
