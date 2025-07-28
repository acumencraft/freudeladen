<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Response;
use common\models\SeoSetting;
use common\models\SeoPage;
use common\models\BlogPost;
use common\models\Product;

/**
 * SeoController implements SEO management functionality
 */
class SeoController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'generate-sitemap' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * SEO Dashboard
     * @return string
     */
    public function actionIndex()
    {
        // Get SEO overview data
        $totalPages = SeoPage::find()->where(['is_active' => 1])->count();
        $totalSettings = SeoSetting::find()->where(['is_active' => 1])->count();
        
        // Get recent SEO pages
        $recentPages = SeoPage::find()
            ->where(['is_active' => 1])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // Get SEO health metrics
        $seoHealth = $this->calculateSeoHealth();

        return $this->render('index', [
            'totalPages' => $totalPages,
            'totalSettings' => $totalSettings,
            'recentPages' => $recentPages,
            'seoHealth' => $seoHealth,
        ]);
    }

    /**
     * SEO Settings Management
     * @return string
     */
    public function actionSettings()
    {
        if (Yii::$app->request->isPost) {
            $settings = Yii::$app->request->post('settings', []);
            
            foreach ($settings as $key => $value) {
                $setting = SeoSetting::findOne(['key' => $key]);
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                }
            }
            
            Yii::$app->session->setFlash('success', 'SEO settings updated successfully.');
            return $this->redirect(['settings']);
        }

        // Group settings by category
        $generalSettings = SeoSetting::find()->where(['group' => SeoSetting::GROUP_GENERAL, 'is_active' => 1])->all();
        $metaSettings = SeoSetting::find()->where(['group' => SeoSetting::GROUP_META, 'is_active' => 1])->all();
        $socialSettings = SeoSetting::find()->where(['group' => SeoSetting::GROUP_SOCIAL, 'is_active' => 1])->all();
        $analyticsSettings = SeoSetting::find()->where(['group' => SeoSetting::GROUP_ANALYTICS, 'is_active' => 1])->all();
        $sitemapSettings = SeoSetting::find()->where(['group' => SeoSetting::GROUP_SITEMAP, 'is_active' => 1])->all();

        return $this->render('settings', [
            'generalSettings' => $generalSettings,
            'metaSettings' => $metaSettings,
            'socialSettings' => $socialSettings,
            'analyticsSettings' => $analyticsSettings,
            'sitemapSettings' => $sitemapSettings,
        ]);
    }

    /**
     * SEO Pages Management
     * @return string
     */
    public function actionPages()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => SeoPage::find()->orderBy(['updated_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('pages', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create new SEO page
     * @return string|Response
     */
    public function actionCreatePage()
    {
        $model = new SeoPage();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'SEO page created successfully.');
            return $this->redirect(['pages']);
        }

        return $this->render('create-page', [
            'model' => $model,
        ]);
    }

    /**
     * Update SEO page
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdatePage($id)
    {
        $model = $this->findSeoPage($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'SEO page updated successfully.');
            return $this->redirect(['pages']);
        }

        return $this->render('update-page', [
            'model' => $model,
        ]);
    }

    /**
     * Delete SEO page
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeletePage($id)
    {
        $this->findSeoPage($id)->delete();
        Yii::$app->session->setFlash('success', 'SEO page deleted successfully.');
        return $this->redirect(['pages']);
    }

    /**
     * Generate XML Sitemap
     * @return Response
     */
    public function actionGenerateSitemap()
    {
        $sitemap = $this->generateXmlSitemap();
        
        // Save sitemap to web directory
        $sitemapPath = Yii::getAlias('@frontend/web/sitemap.xml');
        file_put_contents($sitemapPath, $sitemap);
        
        Yii::$app->session->setFlash('success', 'Sitemap generated successfully at /sitemap.xml');
        return $this->redirect(['sitemap']);
    }

    /**
     * Sitemap Management
     * @return string
     */
    public function actionSitemap()
    {
        // Get sitemap statistics
        $sitemapStats = $this->getSitemapStats();
        
        // Check if sitemap file exists
        $sitemapPath = Yii::getAlias('@frontend/web/sitemap.xml');
        $sitemapExists = file_exists($sitemapPath);
        $sitemapLastModified = $sitemapExists ? filemtime($sitemapPath) : null;

        return $this->render('sitemap', [
            'sitemapStats' => $sitemapStats,
            'sitemapExists' => $sitemapExists,
            'sitemapLastModified' => $sitemapLastModified,
        ]);
    }

    /**
     * SEO Analysis
     * @return string
     */
    public function actionAnalysis()
    {
        $analysis = $this->performSeoAnalysis();

        return $this->render('analysis', [
            'analysis' => $analysis,
        ]);
    }

    /**
     * Auto-detect and add pages for SEO optimization
     * @return Response
     */
    public function actionAutoDetectPages()
    {
        $detectedCount = 0;

        // Auto-detect blog posts
        $blogPosts = BlogPost::find()->where(['status' => BlogPost::STATUS_PUBLISHED])->all();
        foreach ($blogPosts as $post) {
            $route = '/blog/view/' . $post->id;
            if (!SeoPage::findOne(['route' => $route])) {
                $seoPage = new SeoPage();
                $seoPage->route = $route;
                $seoPage->title = $post->title . ' - Freudeladen Blog';
                $seoPage->description = substr(strip_tags($post->content), 0, 160);
                $seoPage->keywords = $post->meta_keywords;
                $seoPage->priority = 0.6;
                $seoPage->changefreq = SeoPage::CHANGEFREQ_MONTHLY;
                if ($seoPage->save()) {
                    $detectedCount++;
                }
            }
        }

        // Auto-detect products (if Product model exists)
        if (class_exists('common\models\Product')) {
            $products = Product::find()->where(['status' => 1])->limit(100)->all();
            foreach ($products as $product) {
                $route = '/product/view/' . $product->id;
                if (!SeoPage::findOne(['route' => $route])) {
                    $seoPage = new SeoPage();
                    $seoPage->route = $route;
                    $seoPage->title = $product->name . ' - Freudeladen';
                    $seoPage->description = substr(strip_tags($product->description), 0, 160);
                    $seoPage->priority = 0.8;
                    $seoPage->changefreq = SeoPage::CHANGEFREQ_WEEKLY;
                    if ($seoPage->save()) {
                        $detectedCount++;
                    }
                }
            }
        }

        // Add common pages
        SeoPage::autoDetectPages();
        $detectedCount += 7; // Common pages count

        Yii::$app->session->setFlash('success', "Auto-detected and added {$detectedCount} pages for SEO optimization.");
        return $this->redirect(['pages']);
    }

    /**
     * Generate robots.txt
     * @return Response
     */
    public function actionGenerateRobots()
    {
        $robotsTxt = $this->generateRobotsTxt();
        
        // Save robots.txt to web directory
        $robotsPath = Yii::getAlias('@frontend/web/robots.txt');
        file_put_contents($robotsPath, $robotsTxt);
        
        Yii::$app->session->setFlash('success', 'Robots.txt generated successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Find SEO page model
     * @param int $id
     * @return SeoPage
     * @throws NotFoundHttpException
     */
    protected function findSeoPage($id)
    {
        if (($model = SeoPage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Calculate SEO health score
     * @return array
     */
    private function calculateSeoHealth()
    {
        $score = 0;
        $maxScore = 100;
        $issues = [];

        // Check if basic settings are configured
        $siteTitle = SeoSetting::getValue('site_title');
        if (!empty($siteTitle) && $siteTitle !== 'Freudeladen - Premium Online Shop') {
            $score += 20;
        } else {
            $issues[] = 'Site title needs customization';
        }

        $siteDescription = SeoSetting::getValue('site_description');
        if (!empty($siteDescription) && strlen($siteDescription) > 120) {
            $score += 20;
        } else {
            $issues[] = 'Site description should be at least 120 characters';
        }

        // Check if Google Analytics is configured
        $googleAnalytics = SeoSetting::getValue('google_analytics');
        if (!empty($googleAnalytics)) {
            $score += 15;
        } else {
            $issues[] = 'Google Analytics not configured';
        }

        // Check if sitemap exists
        $sitemapPath = Yii::getAlias('@frontend/web/sitemap.xml');
        if (file_exists($sitemapPath)) {
            $score += 15;
        } else {
            $issues[] = 'XML sitemap not generated';
        }

        // Check if robots.txt exists
        $robotsPath = Yii::getAlias('@frontend/web/robots.txt');
        if (file_exists($robotsPath)) {
            $score += 10;
        } else {
            $issues[] = 'Robots.txt not generated';
        }

        // Check SEO pages coverage
        $totalPages = SeoPage::find()->count();
        if ($totalPages > 10) {
            $score += 20;
        } elseif ($totalPages > 5) {
            $score += 10;
        } else {
            $issues[] = 'More pages need SEO optimization';
        }

        return [
            'score' => $score,
            'maxScore' => $maxScore,
            'percentage' => round(($score / $maxScore) * 100),
            'issues' => $issues,
            'status' => $score >= 80 ? 'excellent' : ($score >= 60 ? 'good' : ($score >= 40 ? 'fair' : 'poor')),
        ];
    }

    /**
     * Generate XML sitemap
     * @return string
     */
    private function generateXmlSitemap()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        // Add SEO pages
        $seoPages = SeoPage::find()->where(['is_active' => 1])->all();
        foreach ($seoPages as $page) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($page->getFullUrl()));
            $url->addChild('lastmod', date('Y-m-d\TH:i:s+00:00', $page->updated_at));
            $url->addChild('changefreq', $page->changefreq ?: 'weekly');
            $url->addChild('priority', $page->priority ?: 0.5);
        }

        return $xml->asXML();
    }

    /**
     * Generate robots.txt
     * @return string
     */
    private function generateRobotsTxt()
    {
        $baseUrl = Yii::$app->urlManager->hostInfo;
        
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /backend/\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "\n";
        $robots .= "Sitemap: {$baseUrl}/sitemap.xml\n";

        return $robots;
    }

    /**
     * Get sitemap statistics
     * @return array
     */
    private function getSitemapStats()
    {
        return [
            'totalUrls' => SeoPage::find()->where(['is_active' => 1])->count(),
            'highPriorityUrls' => SeoPage::find()->where(['>=', 'priority', 0.8])->andWhere(['is_active' => 1])->count(),
            'dailyUpdates' => SeoPage::find()->where(['changefreq' => SeoPage::CHANGEFREQ_DAILY])->andWhere(['is_active' => 1])->count(),
            'weeklyUpdates' => SeoPage::find()->where(['changefreq' => SeoPage::CHANGEFREQ_WEEKLY])->andWhere(['is_active' => 1])->count(),
        ];
    }

    /**
     * Perform SEO analysis
     * @return array
     */
    private function performSeoAnalysis()
    {
        $analysis = [
            'meta_tags' => [],
            'content' => [],
            'technical' => [],
            'recommendations' => [],
        ];

        // Meta tags analysis
        $analysis['meta_tags']['title_configured'] = !empty(SeoSetting::getValue('site_title'));
        $analysis['meta_tags']['description_configured'] = !empty(SeoSetting::getValue('site_description'));
        $analysis['meta_tags']['keywords_configured'] = !empty(SeoSetting::getValue('site_keywords'));

        // Content analysis
        $analysis['content']['pages_optimized'] = SeoPage::find()->where(['is_active' => 1])->count();
        $analysis['content']['blog_posts'] = BlogPost::find()->where(['status' => BlogPost::STATUS_PUBLISHED])->count();

        // Technical analysis
        $analysis['technical']['sitemap_exists'] = file_exists(Yii::getAlias('@frontend/web/sitemap.xml'));
        $analysis['technical']['robots_exists'] = file_exists(Yii::getAlias('@frontend/web/robots.txt'));
        $analysis['technical']['analytics_configured'] = !empty(SeoSetting::getValue('google_analytics'));

        // Generate recommendations
        $analysis['recommendations'] = $this->generateSeoRecommendations($analysis);

        return $analysis;
    }

    /**
     * Generate SEO recommendations
     * @param array $analysis
     * @return array
     */
    private function generateSeoRecommendations($analysis)
    {
        $recommendations = [];

        if (!$analysis['meta_tags']['title_configured']) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Configure Site Title',
                'description' => 'Set a unique and descriptive title for your website.',
                'action' => 'Go to SEO Settings'
            ];
        }

        if (!$analysis['technical']['sitemap_exists']) {
            $recommendations[] = [
                'type' => 'error',
                'title' => 'Generate XML Sitemap',
                'description' => 'Create an XML sitemap to help search engines index your site.',
                'action' => 'Generate Sitemap'
            ];
        }

        if (!$analysis['technical']['analytics_configured']) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Setup Google Analytics',
                'description' => 'Configure Google Analytics to track your website performance.',
                'action' => 'Configure Analytics'
            ];
        }

        if ($analysis['content']['pages_optimized'] < 5) {
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Optimize More Pages',
                'description' => 'Add SEO optimization for more pages to improve search visibility.',
                'action' => 'Auto-detect Pages'
            ];
        }

        return $recommendations;
    }
}
