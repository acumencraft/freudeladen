<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * SettingsController handles system settings management.
 */
class SettingsController extends Controller
{
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Display settings overview.
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * General settings management.
     * @return string|Response
     */
    public function actionGeneral()
    {
        $settings = [
            'site_name' => Yii::$app->params['siteName'] ?? '',
            'site_description' => Yii::$app->params['siteDescription'] ?? '',
            'admin_email' => Yii::$app->params['adminEmail'] ?? '',
            'maintenance_mode' => Yii::$app->params['maintenanceMode'] ?? false,
            'timezone' => Yii::$app->params['timezone'] ?? 'UTC',
            'language' => Yii::$app->params['language'] ?? 'en-US',
        ];

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            
            // Update settings
            foreach ($settings as $key => $value) {
                if (isset($postData[$key])) {
                    $settings[$key] = $postData[$key];
                    Yii::$app->params[$key] = $postData[$key];
                }
            }
            
            // Save to config file (in a real application)
            // This would save to a database or config file
            $this->saveSettings($settings);
            
            Yii::$app->session->setFlash('success', 'General settings updated successfully.');
            return $this->redirect(['general']);
        }

        return $this->render('general', [
            'settings' => $settings,
        ]);
    }

    /**
     * SEO settings management.
     * @return string|Response
     */
    public function actionSeo()
    {
        $settings = [
            'meta_title' => Yii::$app->params['metaTitle'] ?? '',
            'meta_description' => Yii::$app->params['metaDescription'] ?? '',
            'meta_keywords' => Yii::$app->params['metaKeywords'] ?? '',
            'google_analytics_id' => Yii::$app->params['googleAnalyticsId'] ?? '',
            'google_tag_manager_id' => Yii::$app->params['googleTagManagerId'] ?? '',
            'facebook_pixel_id' => Yii::$app->params['facebookPixelId'] ?? '',
            'robots_txt' => Yii::$app->params['robotsTxt'] ?? '',
        ];

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            
            foreach ($settings as $key => $value) {
                if (isset($postData[$key])) {
                    $settings[$key] = $postData[$key];
                    Yii::$app->params[$key] = $postData[$key];
                }
            }
            
            $this->saveSettings($settings);
            
            Yii::$app->session->setFlash('success', 'SEO settings updated successfully.');
            return $this->redirect(['seo']);
        }

        return $this->render('seo', [
            'settings' => $settings,
        ]);
    }

    /**
     * Email settings management.
     * @return string|Response
     */
    public function actionEmail()
    {
        $settings = [
            'smtp_host' => Yii::$app->params['smtpHost'] ?? '',
            'smtp_port' => Yii::$app->params['smtpPort'] ?? 587,
            'smtp_username' => Yii::$app->params['smtpUsername'] ?? '',
            'smtp_password' => Yii::$app->params['smtpPassword'] ?? '',
            'smtp_encryption' => Yii::$app->params['smtpEncryption'] ?? 'tls',
            'from_email' => Yii::$app->params['fromEmail'] ?? '',
            'from_name' => Yii::$app->params['fromName'] ?? '',
        ];

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            
            foreach ($settings as $key => $value) {
                if (isset($postData[$key])) {
                    $settings[$key] = $postData[$key];
                    Yii::$app->params[$key] = $postData[$key];
                }
            }
            
            $this->saveSettings($settings);
            
            Yii::$app->session->setFlash('success', 'Email settings updated successfully.');
            return $this->redirect(['email']);
        }

        return $this->render('email', [
            'settings' => $settings,
        ]);
    }

    /**
     * Payment settings management.
     * @return string|Response
     */
    public function actionPayment()
    {
        $settings = [
            'currency' => Yii::$app->params['currency'] ?? 'USD',
            'currency_symbol' => Yii::$app->params['currencySymbol'] ?? '$',
            'tax_rate' => Yii::$app->params['taxRate'] ?? 0,
            'stripe_public_key' => Yii::$app->params['stripePublicKey'] ?? '',
            'stripe_secret_key' => Yii::$app->params['stripeSecretKey'] ?? '',
            'paypal_client_id' => Yii::$app->params['paypalClientId'] ?? '',
            'paypal_secret' => Yii::$app->params['paypalSecret'] ?? '',
            'paypal_sandbox' => Yii::$app->params['paypalSandbox'] ?? true,
        ];

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            
            foreach ($settings as $key => $value) {
                if (isset($postData[$key])) {
                    $settings[$key] = $postData[$key];
                    Yii::$app->params[$key] = $postData[$key];
                }
            }
            
            $this->saveSettings($settings);
            
            Yii::$app->session->setFlash('success', 'Payment settings updated successfully.');
            return $this->redirect(['payment']);
        }

        return $this->render('payment', [
            'settings' => $settings,
        ]);
    }

    /**
     * Test email configuration.
     * @return Response
     */
    public function actionTestEmail()
    {
        $email = Yii::$app->request->post('email');
        
        if (!$email) {
            Yii::$app->session->setFlash('error', 'Please provide an email address.');
            return $this->redirect(['email']);
        }

        try {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setSubject('Test Email from ' . Yii::$app->params['siteName'])
                ->setTextBody('This is a test email to verify your email configuration.')
                ->send();
                
            Yii::$app->session->setFlash('success', 'Test email sent successfully to ' . $email);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Failed to send test email: ' . $e->getMessage());
        }

        return $this->redirect(['email']);
    }

    /**
     * Clear cache action.
     * @return Response
     */
    public function actionClearCache()
    {
        try {
            Yii::$app->cache->flush();
            Yii::$app->session->setFlash('success', 'Cache cleared successfully.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Failed to clear cache: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * System information display.
     * @return string
     */
    public function actionSystemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'yii_version' => Yii::getVersion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'extensions' => get_loaded_extensions(),
        ];

        return $this->render('system-info', [
            'info' => $info,
        ]);
    }

    /**
     * Database backup.
     * @return Response
     */
    public function actionBackupDatabase()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = Yii::getAlias('@runtime/backups/') . $filename;
            
            // Create backups directory if it doesn't exist
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            
            // Get database configuration
            $db = Yii::$app->db;
            $dsn = $db->dsn;
            $username = $db->username;
            $password = $db->password;
            
            // Extract database name from DSN
            preg_match('/dbname=([^;]+)/', $dsn, $matches);
            $dbname = $matches[1] ?? 'database';
            
            // Create backup command
            $command = "mysqldump -u{$username} -p{$password} {$dbname} > {$filepath}";
            
            // Execute backup
            exec($command, $output, $return_var);
            
            if ($return_var === 0) {
                Yii::$app->session->setFlash('success', 'Database backup created successfully: ' . $filename);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to create database backup.');
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Backup failed: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Save settings to storage.
     * In a real application, this would save to database or config file.
     * @param array $settings
     */
    private function saveSettings($settings)
    {
        // This is a placeholder implementation
        // In a real application, you would save to database or update config files
        foreach ($settings as $key => $value) {
            // Save to database or config file
            // Example: Settings::updateAll(['value' => $value], ['key' => $key]);
        }
    }
}
