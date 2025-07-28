<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;

class UserController extends Controller
{
    /**
     * Create a test user
     */
    public function actionCreate($username, $email, $password)
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        
        if ($user->save()) {
            echo "User '{$username}' created successfully with ID: {$user->id}\n";
            return 0;
        } else {
            echo "Failed to create user. Errors:\n";
            foreach ($user->errors as $field => $errors) {
                foreach ($errors as $error) {
                    echo "- {$field}: {$error}\n";
                }
            }
            return 1;
        }
    }
    
    /**
     * Reset admin password
     */
    public function actionResetAdmin($password = 'admin123')
    {
        $user = User::findByUsername('admin');
        if (!$user) {
            echo "Admin user not found\n";
            return 1;
        }
        
        $user->setPassword($password);
        $user->generateAuthKey();
        
        if ($user->save()) {
            echo "Admin password reset to: {$password}\n";
            return 0;
        } else {
            echo "Failed to reset password\n";
            return 1;
        }
    }
    
    /**
     * List all users
     */
    public function actionList()
    {
        $users = User::find()->all();
        echo "All users:\n";
        foreach ($users as $user) {
            echo "- ID: {$user->id}, Username: {$user->username}, Email: {$user->email}, Status: {$user->status}\n";
        }
    }
}
