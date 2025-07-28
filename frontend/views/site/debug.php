<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Debug Info - FREUDELADEN.DE';
?>

<div class="container mt-4">
    <h1>Debug Information</h1>
    
    <div class="card mb-4">
        <div class="card-header">Session Info</div>
        <div class="card-body">
            <p><strong>Session ID:</strong> <?= Yii::$app->session->getId() ?></p>
            <p><strong>Session Status:</strong> <?= Yii::$app->session->getIsActive() ? 'Active' : 'Inactive' ?></p>
            <p><strong>User ID:</strong> <?= Yii::$app->user->isGuest ? 'Guest' : Yii::$app->user->id ?></p>
            <p><strong>User Logged In:</strong> <?= Yii::$app->user->isGuest ? 'No' : 'Yes' ?></p>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Cart Info</div>
        <div class="card-body">
            <?php 
            $sessionId = Yii::$app->session->getId();
            $userId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
            $cartItems = \common\models\Cart::getCartItems($sessionId, $userId);
            $cartCount = \common\models\Cart::getCartCount($sessionId, $userId);
            ?>
            <p><strong>Cart Items Count:</strong> <?= $cartCount ?></p>
            <p><strong>Items:</strong></p>
            <?php if (!empty($cartItems)): ?>
                <ul>
                    <?php foreach ($cartItems as $item): ?>
                        <li>ID: <?= $item->id ?>, Product: <?= $item->product->name ?>, Quantity: <?= $item->quantity ?>, Session: <?= $item->session_id ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No cart items found for current session</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Database Cart Items</div>
        <div class="card-body">
            <?php 
            $allCartItems = \common\models\Cart::find()->all();
            ?>
            <p><strong>All Cart Items in Database:</strong></p>
            <?php if (!empty($allCartItems)): ?>
                <ul>
                    <?php foreach ($allCartItems as $item): ?>
                        <li>ID: <?= $item->id ?>, Session: <?= $item->session_id ?>, Product: <?= $item->product_id ?>, Quantity: <?= $item->quantity ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No cart items in database</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Environment</div>
        <div class="card-body">
            <p><strong>Application Environment:</strong> <?= YII_ENV ?></p>
            <p><strong>Debug Mode:</strong> <?= YII_DEBUG ? 'On' : 'Off' ?></p>
            <p><strong>Current URL:</strong> <?= Yii::$app->request->absoluteUrl ?></p>
        </div>
    </div>
</div>
