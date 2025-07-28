<?php

use yii\db\Migration;

class m250728_125931_create_admin_panel_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Admin User Table
        $this->createTable('admin_user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'last_login_at' => $this->integer(),
        ]);

        // Admin Log Table
        $this->createTable('admin_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'action' => $this->string(255)->notNull(),
            'object_type' => $this->string(255),
            'object_id' => $this->integer(),
            'details' => $this->text(),
            'ip_address' => $this->string(45),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Store Settings Table
        $this->createTable('store_settings', [
            'id' => $this->primaryKey(),
            'store_name' => $this->string(255)->notNull(),
            'store_email' => $this->string(255),
            'store_phone' => $this->string(50),
            'store_address' => $this->text(),
            'currency_code' => $this->string(3)->defaultValue('EUR'),
            'timezone' => $this->string(100)->defaultValue('Europe/Berlin'),
            'working_hours' => $this->text(),
            'logo_path' => $this->string(255),
            'favicon_path' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Banner Table
        $this->createTable('banner', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'subtitle' => $this->text(),
            'image_path' => $this->string(255)->notNull(),
            'link' => $this->string(255),
            'position' => $this->string(50),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'is_active' => $this->tinyInteger()->defaultValue(1),
            'sort_order' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Page Table
        $this->createTable('page', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'content' => $this->text(),
            'meta_title' => $this->string(255),
            'meta_description' => $this->text(),
            'is_in_menu' => $this->tinyInteger()->defaultValue(0),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Email Template Table
        $this->createTable('email_template', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'subject' => $this->string(255)->notNull(),
            'body' => $this->text()->notNull(),
            'variables' => $this->text(),
            'is_active' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Shipping Method Table
        $this->createTable('shipping_method', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'is_active' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Shipping Zone Table
        $this->createTable('shipping_zone', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'countries' => $this->text(),
            'regions' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Shipping Rate Table
        $this->createTable('shipping_rate', [
            'id' => $this->primaryKey(),
            'shipping_method_id' => $this->integer()->notNull(),
            'shipping_zone_id' => $this->integer()->notNull(),
            'min_order_total' => $this->decimal(10, 2)->defaultValue(0),
            'max_order_total' => $this->decimal(10, 2)->defaultValue(999999.99),
            'rate' => $this->decimal(10, 2)->notNull(),
            'is_free_shipping' => $this->tinyInteger()->defaultValue(0),
            'free_shipping_min_amount' => $this->decimal(10, 2),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Payment Method Table
        $this->createTable('payment_method', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'is_active' => $this->tinyInteger()->defaultValue(1),
            'provider' => $this->string(50),
            'configuration' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Payment Transaction Table
        $this->createTable('payment_transaction', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'payment_method_id' => $this->integer(),
            'transaction_id' => $this->string(255),
            'amount' => $this->decimal(10, 2)->notNull(),
            'status' => $this->string(50)->notNull(),
            'details' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Tax Rate Table
        $this->createTable('tax_rate', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'rate' => $this->decimal(5, 2)->notNull(),
            'country' => $this->string(100),
            'state' => $this->string(100),
            'is_default' => $this->tinyInteger()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // SEO Settings Table
        $this->createTable('seo_settings', [
            'id' => $this->primaryKey(),
            'site_name' => $this->string(255)->notNull(),
            'meta_title_template' => $this->string(255),
            'meta_description_template' => $this->text(),
            'default_robots' => $this->string(50)->defaultValue('index,follow'),
            'google_analytics_id' => $this->string(50),
            'google_site_verification' => $this->string(100),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Redirect Table
        $this->createTable('redirect', [
            'id' => $this->primaryKey(),
            'source_url' => $this->string(255)->notNull(),
            'target_url' => $this->string(255)->notNull(),
            'redirect_type' => "ENUM('301', '302') DEFAULT '301'",
            'is_active' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add Foreign Keys
        $this->addForeignKey('fk-admin_log-user_id', 'admin_log', 'user_id', 'admin_user', 'id', 'SET NULL');
        $this->addForeignKey('fk-shipping_rate-method_id', 'shipping_rate', 'shipping_method_id', 'shipping_method', 'id', 'CASCADE');
        $this->addForeignKey('fk-shipping_rate-zone_id', 'shipping_rate', 'shipping_zone_id', 'shipping_zone', 'id', 'CASCADE');
        $this->addForeignKey('fk-payment_transaction-order_id', 'payment_transaction', 'order_id', 'orders', 'id', 'CASCADE');
        $this->addForeignKey('fk-payment_transaction-method_id', 'payment_transaction', 'payment_method_id', 'payment_method', 'id', 'SET NULL');

        // Insert default admin user
        $this->insert('admin_user', [
            'username' => 'admin',
            'auth_key' => \Yii::$app->security->generateRandomString(),
            'password_hash' => \Yii::$app->security->generatePasswordHash('admin123'),
            'email' => 'admin@freudeladen.de',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        // Insert default store settings
        $this->insert('store_settings', [
            'store_name' => 'FREUDELADEN.DE',
            'store_email' => 'info@freudeladen.de',
            'store_phone' => '+49 123 456 789',
            'store_address' => 'Musterstraße 123, 12345 Berlin, Deutschland',
            'currency_code' => 'EUR',
            'timezone' => 'Europe/Berlin',
        ]);

        // Insert default tax rate
        $this->insert('tax_rate', [
            'name' => 'Standard VAT (Deutschland)',
            'rate' => 19.00,
            'country' => 'Deutschland',
            'is_default' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-payment_transaction-method_id', 'payment_transaction');
        $this->dropForeignKey('fk-payment_transaction-order_id', 'payment_transaction');
        $this->dropForeignKey('fk-shipping_rate-zone_id', 'shipping_rate');
        $this->dropForeignKey('fk-shipping_rate-method_id', 'shipping_rate');
        $this->dropForeignKey('fk-admin_log-user_id', 'admin_log');

        // Drop tables
        $this->dropTable('redirect');
        $this->dropTable('seo_settings');
        $this->dropTable('tax_rate');
        $this->dropTable('payment_transaction');
        $this->dropTable('payment_method');
        $this->dropTable('shipping_rate');
        $this->dropTable('shipping_zone');
        $this->dropTable('shipping_method');
        $this->dropTable('email_template');
        $this->dropTable('page');
        $this->dropTable('banner');
        $this->dropTable('store_settings');
        $this->dropTable('admin_log');
        $this->dropTable('admin_user');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250728_125931_create_admin_panel_tables cannot be reverted.\n";

        return false;
    }
    */
}
