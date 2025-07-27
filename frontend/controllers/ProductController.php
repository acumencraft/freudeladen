<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use common\models\Product;
use common\models\Category;

/**
 * Product controller for the frontend application
 */
class ProductController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all products.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Product::find()
            ->where(['status' => Product::STATUS_ACTIVE])
            ->with(['category', 'images']);

        // Filter by category if provided
        $categorySlug = Yii::$app->request->get('category');
        if ($categorySlug) {
            $category = Category::find()->where(['slug' => $categorySlug])->one();
            if ($category) {
                $query->andWhere(['category_id' => $category->id]);
            }
        }

        // Filter by search query
        $search = Yii::$app->request->get('search');
        if ($search) {
            $query->andWhere(['like', 'name', $search])
                  ->orWhere(['like', 'description', $search]);
        }

        // Sort products
        $sort = Yii::$app->request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price ASC');
                break;
            case 'price_desc':
                $query->orderBy('price DESC');
                break;
            case 'name':
                $query->orderBy('name ASC');
                break;
            default:
                $query->orderBy('created_at DESC');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $categories = Category::getActiveCategories();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'categories' => $categories,
            'currentCategory' => isset($category) ? $category : null,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param string $slug
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($slug)
    {
        $product = $this->findProductBySlug($slug);
        
        // Get related products from the same category
        $relatedProducts = [];
        if ($product->category_id) {
            $relatedProducts = Product::find()
                ->where(['category_id' => $product->category_id, 'status' => Product::STATUS_ACTIVE])
                ->andWhere(['!=', 'id', $product->id])
                ->limit(4)
                ->all();
        }

        return $this->render('view', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Category page
     * @param string $slug
     * @return mixed
     * @throws NotFoundHttpException if the category cannot be found
     */
    public function actionCategory($slug)
    {
        $category = Category::find()
            ->where(['slug' => $slug, 'status' => Category::STATUS_ACTIVE])
            ->one();

        if (!$category) {
            throw new NotFoundHttpException('The requested category does not exist.');
        }

        $query = Product::find()
            ->where(['category_id' => $category->id, 'status' => Product::STATUS_ACTIVE])
            ->with(['images']);

        // Sort products
        $sort = Yii::$app->request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price ASC');
                break;
            case 'price_desc':
                $query->orderBy('price DESC');
                break;
            case 'name':
                $query->orderBy('name ASC');
                break;
            default:
                $query->orderBy('created_at DESC');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('category', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Product model based on its slug value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $slug
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findProductBySlug($slug)
    {
        $model = Product::find()
            ->where(['slug' => $slug, 'status' => Product::STATUS_ACTIVE])
            ->with(['category', 'images', 'variants'])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested product does not exist.');
    }
}
