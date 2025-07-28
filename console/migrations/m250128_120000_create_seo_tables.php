<?php

use yii\db\Migration;

/**
 * Handles the creation of SEO management tables
 */
class m250128_120000_create_seo_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create seo_setting table
        $this->createTable('{{%seo_setting}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(100)->notNull()->unique(),
            'value' => $this->text()->notNull(),
            'description' => $this->text(),
            'group' => $this->string(50)->defaultValue('general'),
            'is_active' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Create index for better performance
        $this->createIndex('idx-seo_setting-key', '{{%seo_setting}}', 'key');
        $this->createIndex('idx-seo_setting-group', '{{%seo_setting}}', 'group');
        $this->createIndex('idx-seo_setting-is_active', '{{%seo_setting}}', 'is_active');

        // Create seo_page table
        $this->createTable('{{%seo_page}}', [
            'id' => $this->primaryKey(),
            'route' => $this->string(255)->notNull()->unique(),
            'title' => $this->string(200),
            'description' => $this->text(),
            'keywords' => $this->string(500),
            'canonical_url' => $this->string(255),
            'og_title' => $this->string(200),
            'og_description' => $this->text(),
            'og_image' => $this->string(255),
            'robots' => $this->string(100)->defaultValue('index, follow'),
            'priority' => $this->decimal(2, 1)->defaultValue(0.5),
            'changefreq' => $this->string(20)->defaultValue('weekly'),
            'is_active' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // Create indexes for better performance
        $this->createIndex('idx-seo_page-route', '{{%seo_page}}', 'route');
        $this->createIndex('idx-seo_page-is_active', '{{%seo_page}}', 'is_active');
        $this->createIndex('idx-seo_page-priority', '{{%seo_page}}', 'priority');

        // Insert default SEO settings
        $this->insertDefaultSeoSettings();
        
        // Insert default SEO pages
        $this->insertDefaultSeoPages();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%seo_page}}');
        $this->dropTable('{{%seo_setting}}');
    }

    /**
     * Insert default SEO settings
     */
    private function insertDefaultSeoSettings()
    {
        $now = time();
        
        $defaultSettings = [
            // General SEO
            ['site_title', 'Freudeladen - Premium Online Shop', 'general', 'Main site title'],
            ['site_description', 'Discover premium products at Freudeladen. Quality, style, and exceptional service.', 'general', 'Main site description'],
            ['site_keywords', 'online shop, premium products, quality, freudeladen', 'general', 'Main site keywords'],
            ['site_author', 'Freudeladen Team', 'general', 'Site author'],
            
            // Meta Tags
            ['meta_viewport', 'width=device-width, initial-scale=1', 'meta', 'Viewport meta tag'],
            ['meta_charset', 'UTF-8', 'meta', 'Character encoding'],
            ['meta_robots', 'index, follow', 'meta', 'Default robots directive'],
            
            // Social Media
            ['og_site_name', 'Freudeladen', 'social', 'Open Graph site name'],
            ['og_type', 'website', 'social', 'Open Graph type'],
            ['twitter_card', 'summary_large_image', 'social', 'Twitter card type'],
            ['twitter_site', '@freudeladen', 'social', 'Twitter site handle'],
            
            // Analytics
            ['google_analytics', '', 'analytics', 'Google Analytics tracking ID'],
            ['google_tag_manager', '', 'analytics', 'Google Tag Manager ID'],
            ['facebook_pixel', '', 'analytics', 'Facebook Pixel ID'],
            
            // Sitemap
            ['sitemap_frequency', 'weekly', 'sitemap', 'Sitemap update frequency'],
            ['sitemap_priority', '0.8', 'sitemap', 'Default sitemap priority'],
            ['sitemap_auto_generate', '1', 'sitemap', 'Auto-generate sitemap'],
        ];

        foreach ($defaultSettings as $setting) {
            $this->insert('{{%seo_setting}}', [
                'key' => $setting[0],
                'value' => $setting[1],
                'group' => $setting[2],
                'description' => $setting[3],
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Insert default SEO pages
     */
    private function insertDefaultSeoPages()
    {
        $now = time();
        
        $defaultPages = [
            ['/', 'Freudeladen - Premium Online Shop', 'Discover premium products with exceptional quality and service.', 1.0, 'daily'],
            ['/site/about', 'About Us - Freudeladen', 'Learn more about our story, mission, and commitment to quality.', 0.8, 'monthly'],
            ['/site/contact', 'Contact Us - Freudeladen', 'Get in touch with our customer service team.', 0.8, 'monthly'],
            ['/site/faq', 'Frequently Asked Questions - Freudeladen', 'Find answers to common questions about our products and services.', 0.7, 'weekly'],
            ['/site/shipping', 'Shipping Information - Freudeladen', 'Learn about our shipping options and delivery times.', 0.7, 'monthly'],
            ['/site/returns', 'Returns & Refunds - Freudeladen', 'Our hassle-free return and refund policy.', 0.6, 'monthly'],
            ['/site/customer-service', 'Customer Service - Freudeladen', 'Professional customer support for all your needs.', 0.6, 'monthly'],
        ];

        foreach ($defaultPages as $page) {
            $this->insert('{{%seo_page}}', [
                'route' => $page[0],
                'title' => $page[1],
                'description' => $page[2],
                'priority' => $page[3],
                'changefreq' => $page[4],
                'robots' => 'index, follow',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
