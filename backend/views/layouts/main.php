<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            width: 240px;
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 0.75rem 1rem;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 16px;
        }
        
        .navbar-brand {
            color: #fff !important;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .top-navbar {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        .main-content {
            margin-left: 240px;
            padding-top: 56px;
            min-height: 100vh;
        }
        
        .content-wrapper {
            padding: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                top: 5rem;
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
                padding-top: 0;
            }
        }
    </style>
</head>
<body class="h-100">
<?php $this->beginBody() ?>

<!-- Top Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top top-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>">
            <i class="fas fa-store me-2"></i>FREUDELADEN.DE Admin
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= Yii::$app->user->identity->getDisplayName() ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Einstellungen</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'mb-0']) ?>
                            <?= Html::submitButton('<i class="fas fa-sign-out-alt me-2"></i>Abmelden', [
                                'class' => 'dropdown-item',
                                'style' => 'background: none; border: none; width: 100%; text-align: left; padding: 0.375rem 1rem;'
                            ]) ?>
                            <?= Html::endForm() ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<nav class="sidebar d-md-block">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?= Yii::$app->urlManager->createUrl(['dashboard/index']) ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Yii::$app->urlManager->createUrl(['product/index']) ?>">
                    <i class="fas fa-box"></i>
                    Produkte
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Yii::$app->urlManager->createUrl(['category/index']) ?>">
                    <i class="fas fa-tags"></i>
                    Kategorien
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Yii::$app->urlManager->createUrl(['order/index']) ?>">
                    <i class="fas fa-shopping-cart"></i>
                    Bestellungen
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Yii::$app->urlManager->createUrl(['user/index']) ?>">
                    <i class="fas fa-users"></i>
                    Kunden
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Yii::$app->urlManager->createUrl(['banner/index']) ?>">
                    <i class="fas fa-image"></i>
                    Banner
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Yii::$app->urlManager->createUrl(['page/index']) ?>">
                    <i class="fas fa-file-alt"></i>
                    Seiten
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= \yii\helpers\Url::to(['/blog/index']) ?>">
                    <i class="fas fa-blog"></i>
                    Blog Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= \yii\helpers\Url::to(['/blog-category/index']) ?>">
                    <i class="fas fa-folder"></i>
                    Blog Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= \yii\helpers\Url::to(['/faq/index']) ?>">
                    <i class="fas fa-question-circle"></i>
                    FAQs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= \yii\helpers\Url::to(['/faq-category/index']) ?>">
                    <i class="fas fa-folder-open"></i>
                    FAQ Categories
                </a>
            </li>
            
            <!-- Shipping Management -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-shipping-fast"></i>
                    Shipping Management
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/shipping-zone/index']) ?>">
                        <i class="fas fa-globe"></i> Shipping Zones
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/shipping-method/index']) ?>">
                        <i class="fas fa-truck"></i> Shipping Methods
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/shipping-rate/index']) ?>">
                        <i class="fas fa-calculator"></i> Shipping Rates
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/shipping-rate/calculate']) ?>">
                        <i class="fas fa-search-dollar"></i> Rate Calculator
                    </a></li>
                </ul>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-credit-card"></i>
                    Payment Management
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/payment-method/index']) ?>">
                        <i class="fas fa-credit-card"></i> Payment Methods
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/transaction/index']) ?>">
                        <i class="fas fa-exchange-alt"></i> Transactions
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/transaction/analytics']) ?>">
                        <i class="fas fa-chart-line"></i> Payment Analytics
                    </a></li>
                </ul>
            </li>
            
            <!-- Analytics Dashboard -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-chart-bar"></i>
                    Analytics
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/analytics/index']) ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/analytics/sales']) ?>">
                        <i class="fas fa-chart-line"></i> Sales Analytics
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/analytics/payments']) ?>">
                        <i class="fas fa-credit-card"></i> Payment Analytics
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/analytics/users']) ?>">
                        <i class="fas fa-users"></i> User Analytics
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/analytics/generate-report']) ?>">
                        <i class="fas fa-file-export"></i> Generate Report
                    </a></li>
                </ul>
            </li>
            
            <!-- SEO Management -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-search"></i>
                    SEO Management
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/seo/index']) ?>">
                        <i class="fas fa-tachometer-alt"></i> SEO Dashboard
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/seo/settings']) ?>">
                        <i class="fas fa-cogs"></i> SEO Settings
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/seo/pages']) ?>">
                        <i class="fas fa-file-alt"></i> SEO Pages
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/seo/sitemap']) ?>">
                        <i class="fas fa-sitemap"></i> Sitemap Management
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/seo/analysis']) ?>">
                        <i class="fas fa-chart-line"></i> SEO Analysis
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/seo/auto-detect-pages']) ?>">
                        <i class="fas fa-magic"></i> Auto-detect Pages
                    </a></li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-cogs"></i>
                    Einstellungen
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Main Content -->
<main class="main-content">
    <div class="content-wrapper">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
