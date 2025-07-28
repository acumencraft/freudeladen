<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "seo_setting".
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string|null $description
 * @property string|null $group
 * @property int $is_active
 * @property int $created_at
 * @property int $updated_at
 */
class SeoSetting extends ActiveRecord
{
    const GROUP_GENERAL = 'general';
    const GROUP_META = 'meta';
    const GROUP_SOCIAL = 'social';
    const GROUP_ANALYTICS = 'analytics';
    const GROUP_SITEMAP = 'sitemap';
    const GROUP_ROBOTS = 'robots';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seo_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'required'],
            [['value', 'description'], 'string'],
            [['is_active', 'created_at', 'updated_at'], 'integer'],
            [['key'], 'string', 'max' => 100],
            [['group'], 'string', 'max' => 50],
            [['key'], 'unique'],
            [['is_active'], 'default', 'value' => 1],
            [['group'], 'in', 'range' => [
                self::GROUP_GENERAL,
                self::GROUP_META,
                self::GROUP_SOCIAL,
                self::GROUP_ANALYTICS,
                self::GROUP_SITEMAP,
                self::GROUP_ROBOTS
            ]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Setting Key',
            'value' => 'Value',
            'description' => 'Description',
            'group' => 'Group',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get setting value by key
     * @param string $key
     * @param mixed $default
     * @return string|null
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::findOne(['key' => $key, 'is_active' => 1]);
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value
     * @param string $key
     * @param string $value
     * @param string $group
     * @param string $description
     * @return bool
     */
    public static function setValue($key, $value, $group = self::GROUP_GENERAL, $description = null)
    {
        $setting = static::findOne(['key' => $key]);
        if (!$setting) {
            $setting = new static();
            $setting->key = $key;
            $setting->group = $group;
            $setting->description = $description;
        }
        $setting->value = $value;
        return $setting->save();
    }

    /**
     * Get all settings by group
     * @param string $group
     * @return array
     */
    public static function getByGroup($group)
    {
        $settings = static::find()
            ->where(['group' => $group, 'is_active' => 1])
            ->all();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        return $result;
    }

    /**
     * Get group labels
     * @return array
     */
    public static function getGroupLabels()
    {
        return [
            self::GROUP_GENERAL => 'General SEO',
            self::GROUP_META => 'Meta Tags',
            self::GROUP_SOCIAL => 'Social Media',
            self::GROUP_ANALYTICS => 'Analytics',
            self::GROUP_SITEMAP => 'Sitemap',
            self::GROUP_ROBOTS => 'Robots.txt',
        ];
    }

    /**
     * Get group label
     * @return string
     */
    public function getGroupLabel()
    {
        $labels = static::getGroupLabels();
        return $labels[$this->group] ?? $this->group;
    }

    /**
     * Initialize default SEO settings
     */
    public static function initializeDefaults()
    {
        $defaults = [
            // General SEO
            ['site_title', 'Freudeladen - Premium Online Shop', self::GROUP_GENERAL, 'Main site title'],
            ['site_description', 'Discover premium products at Freudeladen. Quality, style, and exceptional service.', self::GROUP_GENERAL, 'Main site description'],
            ['site_keywords', 'online shop, premium products, quality, freudeladen', self::GROUP_GENERAL, 'Main site keywords'],
            ['site_author', 'Freudeladen Team', self::GROUP_GENERAL, 'Site author'],
            
            // Meta Tags
            ['meta_viewport', 'width=device-width, initial-scale=1', self::GROUP_META, 'Viewport meta tag'],
            ['meta_charset', 'UTF-8', self::GROUP_META, 'Character encoding'],
            ['meta_robots', 'index, follow', self::GROUP_META, 'Default robots directive'],
            
            // Social Media
            ['og_site_name', 'Freudeladen', self::GROUP_SOCIAL, 'Open Graph site name'],
            ['og_type', 'website', self::GROUP_SOCIAL, 'Open Graph type'],
            ['twitter_card', 'summary_large_image', self::GROUP_SOCIAL, 'Twitter card type'],
            ['twitter_site', '@freudeladen', self::GROUP_SOCIAL, 'Twitter site handle'],
            
            // Analytics
            ['google_analytics', '', self::GROUP_ANALYTICS, 'Google Analytics tracking ID'],
            ['google_tag_manager', '', self::GROUP_ANALYTICS, 'Google Tag Manager ID'],
            ['facebook_pixel', '', self::GROUP_ANALYTICS, 'Facebook Pixel ID'],
            
            // Sitemap
            ['sitemap_frequency', 'weekly', self::GROUP_SITEMAP, 'Sitemap update frequency'],
            ['sitemap_priority', '0.8', self::GROUP_SITEMAP, 'Default sitemap priority'],
            ['sitemap_auto_generate', '1', self::GROUP_SITEMAP, 'Auto-generate sitemap'],
        ];

        foreach ($defaults as $default) {
            if (!static::findOne(['key' => $default[0]])) {
                static::setValue($default[0], $default[1], $default[2], $default[3]);
            }
        }
    }
}
