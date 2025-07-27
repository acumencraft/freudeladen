<?php

use yii\db\Migration;

/**
 * Creates the database schema for FREUDELADEN.DE
 */
class m250127_221800_create_freudeladen_schema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Categories table
        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->null(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'description' => $this->text(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key for parent_id
        $this->addForeignKey(
            'fk-categories-parent_id',
            '{{%categories}}',
            'parent_id',
            '{{%categories}}',
            'id',
            'CASCADE'
        );

        // Products table
        $this->createTable('{{%products}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->null(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'description' => $this->text(),
            'short_description' => $this->text(),
            'price' => $this->decimal(10, 2)->notNull(),
            'sale_price' => $this->decimal(10, 2)->null(),
            'stock' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key for category_id
        $this->addForeignKey(
            'fk-products-category_id',
            '{{%products}}',
            'category_id',
            '{{%categories}}',
            'id',
            'SET NULL'
        );

        // Product variants table
        $this->createTable('{{%product_variants}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'sku' => $this->string(50),
            'name' => $this->string(255)->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'stock' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key for product_id
        $this->addForeignKey(
            'fk-product_variants-product_id',
            '{{%product_variants}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        // Product images table
        $this->createTable('{{%product_images}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'image_url' => $this->string(500)->notNull(),
            'alt_text' => $this->string(255),
            'sort_order' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key for product_id
        $this->addForeignKey(
            'fk-product_images-product_id',
            '{{%product_images}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        // Orders table
        $this->createTable('{{%orders}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'status' => $this->string(50)->defaultValue('pending'),
            'total' => $this->decimal(10, 2)->notNull(),
            'payment_method' => $this->string(50),
            'payment_status' => $this->string(50)->defaultValue('pending'),
            'shipping_address' => $this->text(),
            'billing_address' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign key for user_id
        $this->addForeignKey(
            'fk-orders-user_id',
            '{{%orders}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL'
        );

        // Order items table
        $this->createTable('{{%order_items}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'variant_id' => $this->integer()->null(),
            'quantity' => $this->integer()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Add foreign keys
        $this->addForeignKey(
            'fk-order_items-order_id',
            '{{%order_items}}',
            'order_id',
            '{{%orders}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order_items-product_id',
            '{{%order_items}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order_items-variant_id',
            '{{%order_items}}',
            'variant_id',
            '{{%product_variants}}',
            'id',
            'SET NULL'
        );

        // Blog categories table
        $this->createTable('{{%blog_categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Blog posts table
        $this->createTable('{{%blog_posts}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->null(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'content' => $this->text(),
            'excerpt' => $this->text(),
            'featured_image' => $this->string(500),
            'author_id' => $this->integer()->null(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Add foreign keys for blog
        $this->addForeignKey(
            'fk-blog_posts-category_id',
            '{{%blog_posts}}',
            'category_id',
            '{{%blog_categories}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-blog_posts-author_id',
            '{{%blog_posts}}',
            'author_id',
            '{{%user}}',
            'id',
            'SET NULL'
        );

        // Static pages table
        $this->createTable('{{%static_pages}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(255)->notNull()->unique(),
            'content' => $this->text(),
            'is_in_menu' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // SEO meta table
        $this->createTable('{{%seo_meta}}', [
            'id' => $this->primaryKey(),
            'entity_type' => $this->string(50)->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'meta_title' => $this->string(255),
            'meta_description' => $this->text(),
            'meta_keywords' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

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

        // Add foreign keys for cart
        $this->addForeignKey(
            'fk-cart-user_id',
            '{{%cart}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-cart-product_id',
            '{{%cart}}',
            'product_id',
            '{{%products}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-cart-variant_id',
            '{{%cart}}',
            'variant_id',
            '{{%product_variants}}',
            'id',
            'CASCADE'
        );

        // Create indexes for better performance
        $this->createIndex('idx-categories-parent_id', '{{%categories}}', 'parent_id');
        $this->createIndex('idx-categories-slug', '{{%categories}}', 'slug');
        $this->createIndex('idx-products-category_id', '{{%products}}', 'category_id');
        $this->createIndex('idx-products-slug', '{{%products}}', 'slug');
        $this->createIndex('idx-products-status', '{{%products}}', 'status');
        $this->createIndex('idx-blog_posts-slug', '{{%blog_posts}}', 'slug');
        $this->createIndex('idx-blog_posts-status', '{{%blog_posts}}', 'status');
        $this->createIndex('idx-static_pages-slug', '{{%static_pages}}', 'slug');
        $this->createIndex('idx-seo_meta-entity', '{{%seo_meta}}', ['entity_type', 'entity_id']);
        $this->createIndex('idx-cart-session_id', '{{%cart}}', 'session_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop tables in reverse order to avoid foreign key constraints
        $this->dropTable('{{%cart}}');
        $this->dropTable('{{%site_settings}}');
        $this->dropTable('{{%seo_meta}}');
        $this->dropTable('{{%static_pages}}');
        $this->dropTable('{{%blog_posts}}');
        $this->dropTable('{{%blog_categories}}');
        $this->dropTable('{{%order_items}}');
        $this->dropTable('{{%orders}}');
        $this->dropTable('{{%product_images}}');
        $this->dropTable('{{%product_variants}}');
        $this->dropTable('{{%products}}');
        $this->dropTable('{{%categories}}');
    }
}
