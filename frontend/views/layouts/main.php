<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use common\models\Category;
use common\models\Cart;

AppAsset::register($this);

// Get categories for navigation
$categories = Category::getActiveCategories();

// Get cart count
$sessionId = Yii::$app->session->getId();
$userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;

// Check if current session has items, if not find any session with items
$currentSessionItems = Cart::find()->where(['session_id' => $sessionId])->count();
if ($currentSessionItems == 0) {
    $anyItemWithSession = Cart::find()->one();
    if ($anyItemWithSession) {
        $sessionId = $anyItemWithSession->session_id;
    }
}

$cartCount = Cart::getCartCount($sessionId, $userId);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="de" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - FREUDELADEN.DE</title>
    <?php $this->head() ?>
    <style>
        :root {
            --primary-color: #212529;
            --bg-color: #ffffff;
        }
        body {
            background-color: var(--bg-color);
            color: var(--primary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .navbar {
            border-bottom: 1px solid #e9ecef;
        }
        .nav-link {
            color: var(--primary-color) !important;
        }
        .nav-link:hover {
            color: #6c757d !important;
        }
        .cart-count {
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        footer {
            border-top: 1px solid #e9ecef;
            margin-top: auto;
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => 'FREUDELADEN.DE',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-md navbar-light bg-white',
        ],
    ]);
    
    // Build categories menu
    $categoryItems = [];
    foreach ($categories as $category) {
        $categoryItems[] = [
            'label' => $category->name, 
            'url' => ['/product/category', 'slug' => $category->slug]
        ];
    }
    
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        [
            'label' => 'Kategorien',
            'items' => $categoryItems,
        ],
        ['label' => 'Alle Produkte', 'url' => ['/product/index']],
    ];
    
    // Right side menu items
    $rightMenuItems = [
        [
            'label' => '<i class="fas fa-shopping-cart"></i> Warenkorb <span id="cart-count" class="badge bg-primary cart-count" style="' . ($cartCount > 0 ? '' : 'display: none;') . '">' . $cartCount . '</span>',
            'url' => ['/cart/index'],
            'encode' => false,
        ],
    ];
    
    if (Yii::$app->user->isGuest) {
        $rightMenuItems[] = ['label' => 'Anmelden', 'url' => ['/site/login']];
        $rightMenuItems[] = ['label' => 'Registrieren', 'url' => ['/site/signup']];
    } else {
        $rightMenuItems[] = [
            'label' => 'Mein Konto (' . Yii::$app->user->identity->username . ')',
            'items' => [
                ['label' => 'Profil', 'url' => ['/user/profile']],
                ['label' => 'Bestellungen', 'url' => ['/user/orders']],
                ['label' => 'Abmelden', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
            ],
        ];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
        'items' => $menuItems,
    ]);
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav mb-2 mb-md-0'],
        'items' => $rightMenuItems,
    ]);
    
    if (false) { // Disabled old code
        echo Html::tag('div',Html::a('Login',['/site/login'],['class' => ['btn btn-link login text-decoration-none']]),['class' => ['d-flex']]);
    } else if (false) { // Also disabled
        echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout text-decoration-none']
            )
            . Html::endForm();
    }
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Kontakt</h5>
                <p class="mb-0">
                    <strong>FREUDELADEN.DE</strong><br>
                    E-Mail: info@freudeladen.de<br>
                    Telefon: +49 (0) 123 456789
                </p>
            </div>
            <div class="col-md-3">
                <h5>Kundenservice</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= \yii\helpers\Url::to(['/site/faq']) ?>" class="text-decoration-none text-dark">FAQ</a></li>
                    <li><a href="<?= \yii\helpers\Url::to(['/site/shipping']) ?>" class="text-decoration-none text-dark">Versand</a></li>
                    <li><a href="<?= \yii\helpers\Url::to(['/site/returns']) ?>" class="text-decoration-none text-dark">Rückgabe</a></li>
                    <li><a href="<?= \yii\helpers\Url::to(['/site/kundenservice']) ?>" class="text-decoration-none text-dark">Kundenservice</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Rechtliches</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-decoration-none text-dark">Impressum</a></li>
                    <li><a href="#" class="text-decoration-none text-dark">Datenschutz</a></li>
                    <li><a href="#" class="text-decoration-none text-dark">AGB</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">&copy; <?= date('Y') ?> FREUDELADEN.DE - Alle Rechte vorbehalten</p>
            </div>
            <div class="col-md-6 text-end">
                <small class="text-muted">Powered by Yii Framework</small>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
