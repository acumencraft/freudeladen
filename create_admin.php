<?php

require_once 'vendor/autoload.php';
require_once 'vendor/yiisoft/yii2/Yii.php';

echo "Loading config...\n";
$config = require('config/web.php');
echo "Creating application...\n";
new yii\web\Application($config);

echo "Generating password hash...\n";
$password = 'admin123';
$hash = Yii::$app->security->generatePasswordHash($password);
$authKey = Yii::$app->security->generateRandomString(32);

echo "Password hash: " . $hash . "\n";
echo "Auth key: " . $authKey . "\n";

// Insert user into database
try {
    echo "Inserting user into database...\n";
    Yii::$app->db->createCommand()->insert('users', [
        'email' => 'admin@freudeladen.de',
        'password_hash' => $hash,
        'auth_key' => $authKey,
        'status' => 10
    ])->execute();
    
    echo "Admin user created successfully!\n";
    echo "Email: admin@freudeladen.de\n";
    echo "Password: admin123\n";
} catch (Exception $e) {
    echo "Error creating user: " . $e->getMessage() . "\n";
}
