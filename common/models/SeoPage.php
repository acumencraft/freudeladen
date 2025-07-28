<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "seo_page".
 *
 * @property int $id
 * @property string $route
 * @property string|null $title
 * @property string|null $description
 * @property string|null $keywords
 * @property string|null $canonical_url
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string|null $robots
 * @property float|null $priority
 * @property string|null $changefreq
 * @property int $is_active
 * @property int $created_at
 * @property int $updated_at
 */
class SeoPage extends ActiveRecord
{
    const CHANGEFREQ_ALWAYS = 'always';
    const CHANGEFREQ_HOURLY = 'hourly';
    const CHANGEFREQ_DAILY = 'daily';
    const CHANGEFREQ_WEEKLY = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY = 'yearly';
    const CHANGEFREQ_NEVER = 'never';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seo_page';
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
            [['route'], 'required'],
            [['description', 'og_description'], 'string'],
            [['priority'], 'number', 'min' => 0, 'max' => 1],
            [['is_active', 'created_at', 'updated_at'], 'integer'],
            [['route', 'canonical_url', 'og_image'], 'string', 'max' => 255],
            [['title', 'og_title'], 'string', 'max' => 200],
            [['keywords'], 'string', 'max' => 500],
            [['robots'], 'string', 'max' => 100],
            [['changefreq'], 'string', 'max' => 20],
            [['route'], 'unique'],
            [['is_active'], 'default', 'value' => 1],
            [['priority'], 'default', 'value' => 0.5],
            [['changefreq'], 'in', 'range' => [
                self::CHANGEFREQ_ALWAYS,
                self::CHANGEFREQ_HOURLY,
                self::CHANGEFREQ_DAILY,
                self::CHANGEFREQ_WEEKLY,
                self::CHANGEFREQ_MONTHLY,
                self::CHANGEFREQ_YEARLY,
                self::CHANGEFREQ_NEVER
            ]],
            [['robots'], 'default', 'value' => 'index, follow'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route' => 'Route/URL',
            'title' => 'Page Title',
            'description' => 'Meta Description',
            'keywords' => 'Meta Keywords',
            'canonical_url' => 'Canonical URL',
            'og_title' => 'Open Graph Title',
            'og_description' => 'Open Graph Description',
            'og_image' => 'Open Graph Image',
            'robots' => 'Robots Directive',
            'priority' => 'Sitemap Priority',
            'changefreq' => 'Change Frequency',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get SEO data for a specific route
     * @param string $route
     * @return array
     */
    public static function getSeoData($route)
    {
        $seoPage = static::findOne(['route' => $route, 'is_active' => 1]);
        
        $defaults = [
            'title' => SeoSetting::getValue('site_title'),
            'description' => SeoSetting::getValue('site_description'),
            'keywords' => SeoSetting::getValue('site_keywords'),
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'robots' => SeoSetting::getValue('meta_robots', 'index, follow'),
        ];

        if ($seoPage) {
            return [
                'title' => $seoPage->title ?: $defaults['title'],
                'description' => $seoPage->description ?: $defaults['description'],
                'keywords' => $seoPage->keywords ?: $defaults['keywords'],
                'canonical_url' => $seoPage->canonical_url,
                'og_title' => $seoPage->og_title ?: $seoPage->title ?: $defaults['title'],
                'og_description' => $seoPage->og_description ?: $seoPage->description ?: $defaults['description'],
                'og_image' => $seoPage->og_image,
                'robots' => $seoPage->robots ?: $defaults['robots'],
            ];
        }

        return $defaults;
    }

    /**
     * Set SEO data for a route
     * @param string $route
     * @param array $data
     * @return bool
     */
    public static function setSeoData($route, $data)
    {
        $seoPage = static::findOne(['route' => $route]);
        if (!$seoPage) {
            $seoPage = new static();
            $seoPage->route = $route;
        }

        $seoPage->setAttributes($data);
        return $seoPage->save();
    }

    /**
     * Get change frequency options
     * @return array
     */
    public static function getChangeFreqOptions()
    {
        return [
            self::CHANGEFREQ_ALWAYS => 'Always',
            self::CHANGEFREQ_HOURLY => 'Hourly',
            self::CHANGEFREQ_DAILY => 'Daily',
            self::CHANGEFREQ_WEEKLY => 'Weekly',
            self::CHANGEFREQ_MONTHLY => 'Monthly',
            self::CHANGEFREQ_YEARLY => 'Yearly',
            self::CHANGEFREQ_NEVER => 'Never',
        ];
    }

    /**
     * Get change frequency label
     * @return string
     */
    public function getChangeFreqLabel()
    {
        $options = static::getChangeFreqOptions();
        return $options[$this->changefreq] ?? $this->changefreq;
    }

    /**
     * Get full URL for this page
     * @return string
     */
    public function getFullUrl()
    {
        if ($this->canonical_url) {
            return $this->canonical_url;
        }
        
        return Url::to($this->route, true);
    }

    /**
     * Generate sitemap entry
     * @return array
     */
    public function getSitemapEntry()
    {
        return [
            'loc' => $this->getFullUrl(),
            'lastmod' => date('Y-m-d\TH:i:s+00:00', $this->updated_at),
            'changefreq' => $this->changefreq ?: 'weekly',
            'priority' => $this->priority ?: 0.5,
        ];
    }

    /**
     * Auto-detect and create SEO pages for common routes
     */
    public static function autoDetectPages()
    {
        $commonRoutes = [
            '/' => ['title' => 'Home', 'priority' => 1.0, 'changefreq' => self::CHANGEFREQ_DAILY],
            '/site/about' => ['title' => 'About Us', 'priority' => 0.8, 'changefreq' => self::CHANGEFREQ_MONTHLY],
            '/site/contact' => ['title' => 'Contact', 'priority' => 0.8, 'changefreq' => self::CHANGEFREQ_MONTHLY],
            '/site/faq' => ['title' => 'FAQ', 'priority' => 0.7, 'changefreq' => self::CHANGEFREQ_WEEKLY],
            '/site/shipping' => ['title' => 'Shipping Information', 'priority' => 0.7, 'changefreq' => self::CHANGEFREQ_MONTHLY],
            '/site/returns' => ['title' => 'Returns & Refunds', 'priority' => 0.6, 'changefreq' => self::CHANGEFREQ_MONTHLY],
            '/site/customer-service' => ['title' => 'Customer Service', 'priority' => 0.6, 'changefreq' => self::CHANGEFREQ_MONTHLY],
        ];

        foreach ($commonRoutes as $route => $data) {
            if (!static::findOne(['route' => $route])) {
                $seoPage = new static();
                $seoPage->route = $route;
                $seoPage->title = $data['title'];
                $seoPage->priority = $data['priority'];
                $seoPage->changefreq = $data['changefreq'];
                $seoPage->save();
            }
        }
    }
}
