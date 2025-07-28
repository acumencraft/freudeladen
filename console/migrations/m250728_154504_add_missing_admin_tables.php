<?php

use yii\db\Migration;

class m250728_154504_add_missing_admin_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create product_seo table
        $this->createTable('{{%product_seo}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull()->unique(),
            'meta_title' => $this->string(255),
            'meta_description' => $this->text(),
            'slug' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        
        $this->addForeignKey(
            'fk-product_seo-product_id',
            '{{%product_seo}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        // Create order_history table
        $this->createTable('{{%order_history}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'status' => $this->string(50)->notNull(),
            'comment' => $this->text(),
            'admin_id' => $this->integer(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        
        $this->addForeignKey(
            'fk-order_history-order_id',
            '{{%order_history}}',
            'order_id',
            '{{%orders}}',
            'id',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk-order_history-admin_id',
            '{{%order_history}}',
            'admin_id',
            '{{%admin_user}}',
            'id',
            'SET NULL'
        );

        // Create user_address table
        $this->createTable('{{%user_address}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => "ENUM('billing', 'shipping') NOT NULL",
            'is_default' => $this->tinyInteger()->defaultValue(0),
            'first_name' => $this->string(100),
            'last_name' => $this->string(100),
            'company' => $this->string(100),
            'address_line1' => $this->string(255),
            'address_line2' => $this->string(255),
            'city' => $this->string(100),
            'state' => $this->string(100),
            'postal_code' => $this->string(20),
            'country' => $this->string(100),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        
        $this->addForeignKey(
            'fk-user_address-user_id',
            '{{%user_address}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // Create shipping_method table
        $this->createTable('{{%shipping_method}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'description' => $this->text(),
            'is_active' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create shipping_zone table
        $this->createTable('{{%shipping_zone}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'countries' => $this->text(),
            'regions' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create shipping_rate table
        $this->createTable('{{%shipping_rate}}', [
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
        
        $this->addForeignKey(
            'fk-shipping_rate-shipping_method_id',
            '{{%shipping_rate}}',
            'shipping_method_id',
            '{{%shipping_method}}',
            'id',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk-shipping_rate-shipping_zone_id',
            '{{%shipping_rate}}',
            'shipping_zone_id',
            '{{%shipping_zone}}',
            'id',
            'CASCADE'
        );

        // Create blog_category table
        $this->createTable('{{%blog_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create blog_post table
        $this->createTable('{{%blog_post}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'content' => $this->text(),
            'excerpt' => $this->text(),
            'category_id' => $this->integer(),
            'featured_image' => $this->string(255),
            'meta_title' => $this->string(255),
            'meta_description' => $this->text(),
            'status' => $this->tinyInteger()->defaultValue(0),
            'published_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        
        $this->addForeignKey(
            'fk-blog_post-category_id',
            '{{%blog_post}}',
            'category_id',
            '{{%blog_category}}',
            'id',
            'SET NULL'
        );

        // Create blog_tag table
        $this->createTable('{{%blog_tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create blog_post_tag table
        $this->createTable('{{%blog_post_tag}}', [
            'post_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ]);
        
        $this->addPrimaryKey('pk-blog_post_tag', '{{%blog_post_tag}}', ['post_id', 'tag_id']);
        
        $this->addForeignKey(
            'fk-blog_post_tag-post_id',
            '{{%blog_post_tag}}',
            'post_id',
            '{{%blog_post}}',
            'id',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk-blog_post_tag-tag_id',
            '{{%blog_post_tag}}',
            'tag_id',
            '{{%blog_tag}}',
            'id',
            'CASCADE'
        );

        // Create faq_category table
        $this->createTable('{{%faq_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'sort_order' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Create faq table
        $this->createTable('{{%faq}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(),
            'question' => $this->text()->notNull(),
            'answer' => $this->text()->notNull(),
            'sort_order' => $this->integer()->defaultValue(0),
            'is_active' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        
        $this->addForeignKey(
            'fk-faq-category_id',
            '{{%faq}}',
            'category_id',
            '{{%faq_category}}',
            'id',
            'SET NULL'
        );

        // Create email_template table
        $this->createTable('{{%email_template}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'subject' => $this->string(255)->notNull(),
            'body' => $this->text()->notNull(),
            'variables' => $this->text(),
            'is_active' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Insert default email templates
        $this->insert('{{%email_template}}', [
            'name' => 'order_confirmation',
            'subject' => 'Bestellbestätigung - Bestellung #{order_number}',
            'body' => 'Liebe/r {customer_name},\n\nvielen Dank für Ihre Bestellung #{order_number}.\n\nBestelldetails:\n{order_details}\n\nGesamtbetrag: {total_amount}\n\nMit freundlichen Grüßen\nIhr Freudeladen Team',
            'variables' => '{"order_number":"Bestellnummer","customer_name":"Kundenname","order_details":"Bestelldetails","total_amount":"Gesamtbetrag"}',
        ]);
        
        $this->insert('{{%email_template}}', [
            'name' => 'order_shipped',
            'subject' => 'Ihre Bestellung wurde versandt - #{order_number}',
            'body' => 'Liebe/r {customer_name},\n\nIhre Bestellung #{order_number} wurde versandt.\n\nTracking-Nummer: {tracking_number}\n\nMit freundlichen Grüßen\nIhr Freudeladen Team',
            'variables' => '{"order_number":"Bestellnummer","customer_name":"Kundenname","tracking_number":"Sendungsverfolgung"}',
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop tables in reverse order to avoid foreign key constraints
        $this->dropTable('{{%email_template}}');
        $this->dropTable('{{%faq}}');
        $this->dropTable('{{%faq_category}}');
        $this->dropTable('{{%blog_post_tag}}');
        $this->dropTable('{{%blog_tag}}');
        $this->dropTable('{{%blog_post}}');
        $this->dropTable('{{%blog_category}}');
        $this->dropTable('{{%shipping_rate}}');
        $this->dropTable('{{%shipping_zone}}');
        $this->dropTable('{{%shipping_method}}');
        $this->dropTable('{{%user_address}}');
        $this->dropTable('{{%order_history}}');
        $this->dropTable('{{%product_seo}}');
        
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250728_154504_add_missing_admin_tables cannot be reverted.\n";

        return false;
    }
    */
}
