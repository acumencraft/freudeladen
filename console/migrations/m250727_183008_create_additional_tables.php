<?php

use yii\db\Migration;

class m250727_183008_create_additional_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Site settings table
        $this->createTable('{{%site_settings}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(100)->notNull()->unique(),
            'value' => $this->text(),
            'description' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Cart table for guest sessions
        $this->createTable('{{%cart}}', [
            'id' => $this->primaryKey(),
            'session_id' => $this->string(255)->notNull(),
            'user_id' => $this->integer()->null(),
            'product_id' => $this->integer()->notNull(),
            'variant_id' => $this->integer()->null(),
            'quantity' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign keys for cart (checking if related tables exist)
        if ($this->db->schema->getTableSchema('user') !== null) {
            $this->addForeignKey(
                'fk-cart-user_id',
                '{{%cart}}',
                'user_id',
                '{{%user}}',
                'id',
                'CASCADE'
            );
        }

        if ($this->db->schema->getTableSchema('products') !== null) {
            $this->addForeignKey(
                'fk-cart-product_id',
                '{{%cart}}',
                'product_id',
                '{{%products}}',
                'id',
                'CASCADE'
            );
        }

        if ($this->db->schema->getTableSchema('product_variants') !== null) {
            $this->addForeignKey(
                'fk-cart-variant_id',
                '{{%cart}}',
                'variant_id',
                '{{%product_variants}}',
                'id',
                'CASCADE'
            );
        }

        // Create indexes for better performance
        $this->createIndex('idx-cart-session_id', '{{%cart}}', 'session_id');
        $this->createIndex('idx-cart-user_id', '{{%cart}}', 'user_id');

        // Insert default site settings
        $this->batchInsert('{{%site_settings}}', ['key', 'value', 'description'], [
            ['site_name', 'FREUDELADEN.DE', 'Website name'],
            ['site_description', 'German e-commerce platform', 'Website description'],
            ['contact_email', 'info@freudeladen.de', 'Contact email address'],
            ['currency', 'EUR', 'Default currency'],
            ['tax_rate', '19', 'Default tax rate in percent'],
            ['shipping_cost', '5.99', 'Default shipping cost'],
            ['free_shipping_threshold', '50.00', 'Free shipping threshold amount'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cart}}');
        $this->dropTable('{{%site_settings}}');
    }
}
