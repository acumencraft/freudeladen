<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - Admin Panel</title>
    <?php $this->head() ?>
    <style>
        .navbar-brand { font-weight: bold; }
        .sidebar { background: #343a40; min-height: 100vh; padding-top: 20px; }
        .sidebar .nav-link { color: #fff; padding: 10px 20px; }
        .sidebar .nav-link:hover { background: #495057; color: #fff; }
        .sidebar .nav-link.active { background: #007bff; color: #fff; }
        .main-content { padding: 20px; }
        .stats-card { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .stats-card h3 { margin: 0; color: #495057; }
        .stats-card .number { font-size: 2em; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin'])) ?>">
                    <?= Html::encode(Yii::$app->name) ?> - Admin
                </a>
            </div>
            <div class="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/site/index'])) ?>">Frontend</a></li>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <li><a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/site/login'])) ?>">Login</a></li>
                    <?php else: ?>
                        <li>
                            <?= Html::beginForm(['/site/logout'], 'post') ?>
                            <?= Html::submitButton(
                                'Logout (' . Html::encode(Yii::$app->user->identity->username) . ')',
                                ['class' => 'btn btn-link logout', 'style' => 'color: #9d9d9d; padding: 15px;']
                            ) ?>
                            <?= Html::endForm() ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid" style="margin-top: 70px;">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/dashboard/index'])) ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/product/index'])) ?>">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/category/index'])) ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/order/index'])) ?>">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/user/index'])) ?>">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/blog/index'])) ?>">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/shipping/index'])) ?>">Shipping</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/admin/settings/index'])) ?>">Settings</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                
                <?php if (Yii::$app->session->hasFlash('success')): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>

                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
