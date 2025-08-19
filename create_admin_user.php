<?php

// Create admin user script
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/web.php';
new yii\web\Application($config);

use app\models\User;

// Create admin user
$user = new User();
$user->username = 'admin';
$user->email = 'admin@freudeladen.de';
$user->setPassword('admin123'); // Simple password for testing
$user->generateAuthKey();
$user->status = User::STATUS_ACTIVE;
$user->created_at = time();
$user->updated_at = time();

if ($user->save()) {
    echo "Admin user created successfully!\n";
    echo "Username: admin\n";
    echo "Email: admin@freudeladen.de\n";
    echo "Password: admin123\n";
} else {
    echo "Error creating admin user:\n";
    print_r($user->getErrors());
}
