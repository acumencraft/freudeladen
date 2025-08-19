<?php

namespace app\modules\admin;

use Yii;
use yii\filters\AccessControl;

/**
 * Admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // Set the layout for admin module
        $this->layout = 'admin';
        
        // Configure admin-specific settings
        $this->defaultRoute = 'dashboard';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Only authenticated users
                    ],
                ],
            ],
        ];
    }
}
