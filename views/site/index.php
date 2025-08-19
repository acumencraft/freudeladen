<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'FREUDELADEN.DE - Welcome';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Welcome to FREUDELADEN.DE!</h1>

        <p class="lead">Your premium e-commerce platform is ready.</p>

        <p><a class="btn btn-lg btn-success" href="/admin">Access Admin Panel</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Product Management</h2>

                <p>Manage your products, categories, and inventory with our comprehensive admin panel.</p>

                <p><a class="btn btn-outline-secondary" href="/admin/product">Products &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Order Management</h2>

                <p>Track orders, manage payments, and handle customer requests efficiently.</p>

                <p><a class="btn btn-outline-secondary" href="/admin/order">Orders &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Content Management</h2>

                <p>Create and manage blog posts, pages, and other content for your store.</p>

                <p><a class="btn btn-outline-secondary" href="/admin/blog">Blog &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
