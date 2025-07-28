<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use common\models\LoginForm;

class TestController extends Controller
{
    /**
     * Test authentication system
     */
    public function actionAuth()
    {
        echo "Testing Authentication System...\n\n";
        
        // Test 1: Find users
        echo "1. Testing user lookup:\n";
        $admin = User::findByUsername('admin');
        $testuser = User::findByUsername('testuser');
        
        if ($admin) {
            echo "   ✓ Admin user found (ID: {$admin->id})\n";
        } else {
            echo "   ✗ Admin user not found\n";
        }
        
        if ($testuser) {
            echo "   ✓ Test user found (ID: {$testuser->id})\n";
        } else {
            echo "   ✗ Test user not found\n";
        }
        
        // Test 2: Password validation
        echo "\n2. Testing password validation:\n";
        if ($admin && $admin->validatePassword('admin123')) {
            echo "   ✓ Admin password validation works\n";
        } else {
            echo "   ✗ Admin password validation failed\n";
        }
        
        if ($testuser && $testuser->validatePassword('test123')) {
            echo "   ✓ Test user password validation works\n";
        } else {
            echo "   ✗ Test user password validation failed\n";
        }
        
        // Test 3: LoginForm validation
        echo "\n3. Testing LoginForm:\n";
        $loginForm = new LoginForm();
        $loginForm->username = 'admin';
        $loginForm->password = 'admin123';
        
        if ($loginForm->validate()) {
            echo "   ✓ LoginForm validation works for admin\n";
        } else {
            echo "   ✗ LoginForm validation failed for admin:\n";
            foreach ($loginForm->errors as $field => $errors) {
                foreach ($errors as $error) {
                    echo "     - {$field}: {$error}\n";
                }
            }
        }
        
        // Test 4: Wrong password
        echo "\n4. Testing wrong password:\n";
        $wrongForm = new LoginForm();
        $wrongForm->username = 'admin';
        $wrongForm->password = 'wrongpassword';
        
        if (!$wrongForm->validate()) {
            echo "   ✓ LoginForm correctly rejects wrong password\n";
        } else {
            echo "   ✗ LoginForm incorrectly accepts wrong password\n";
        }
        
        echo "\nAuthentication test completed!\n";
    }
    
    /**
     * Test registration system
     */
    public function actionSignup()
    {
        echo "Testing Registration System...\n\n";
        
        $signupForm = new \frontend\models\SignupForm();
        $signupForm->username = 'newuser';
        $signupForm->email = 'newuser@freudeladen.de';
        $signupForm->password = 'newpass123';
        
        if ($signupForm->validate()) {
            echo "1. ✓ SignupForm validation passes\n";
            
            // Try to signup
            $user = $signupForm->signup();
            if ($user) {
                echo "2. ✓ User registration successful (ID: {$user->id})\n";
                
                // Test if the new user can be found
                $foundUser = User::findByUsername('newuser');
                if ($foundUser) {
                    echo "3. ✓ New user can be found in database\n";
                    
                    // Test password validation
                    if ($foundUser->validatePassword('newpass123')) {
                        echo "4. ✓ New user password validation works\n";
                    } else {
                        echo "4. ✗ New user password validation failed\n";
                    }
                } else {
                    echo "3. ✗ New user not found in database\n";
                }
            } else {
                echo "2. ✗ User registration failed\n";
                print_r($signupForm->errors);
            }
        } else {
            echo "1. ✗ SignupForm validation failed:\n";
            foreach ($signupForm->errors as $field => $errors) {
                foreach ($errors as $error) {
                    echo "   - {$field}: {$error}\n";
                }
            }
        }
        
        echo "\nRegistration test completed!\n";
    }
}
