<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property int|null $category_id
 * @property string $name
 * @property string $slug
 * @property string|null $sku
 * @property string|null $description
 * @property string|null $short_description
 * @property float $price
 * @property float|null $sale_price
 * @property int $stock
 * @property int $status
 * @property int|null $views
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $category
 * @property ProductVariant[] $variants
 * @property ProductImage[] $images
 * @property OrderItem[] $orderItems
 * @property Cart[] $cartItems
 */
class Product extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
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
            [['category_id', 'stock', 'status', 'views'], 'integer'],
            [['name', 'slug', 'price'], 'required'],
            [['description', 'short_description'], 'string'],
            [['price', 'sale_price'], 'number', 'min' => 0],
            [['name', 'slug'], 'string', 'max' => 255],
            [['sku'], 'string', 'max' => 100],
            [['slug'], 'unique'],
            [['sku'], 'unique'],
            [['slug'], 'match', 'pattern' => '/^[a-z0-9-]+$/', 'message' => 'Slug can only contain lowercase letters, numbers and hyphens.'],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category',
            'name' => 'Name',
            'slug' => 'Slug',
            'sku' => 'SKU',
            'description' => 'Description',
            'short_description' => 'Short Description',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'stock' => 'Stock',
            'status' => 'Status',
            'views' => 'Views',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Variants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVariants()
    {
        return $this->hasMany(ProductVariant::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[Images]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id'])->orderBy('sort_order');
    }

    /**
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[CartItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCartItems()
    {
        return $this->hasMany(Cart::class, ['product_id' => 'id']);
    }

    /**
     * Get active products
     */
    public static function getActiveProducts()
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy('created_at DESC')
            ->all();
    }

    /**
     * Get products on sale
     */
    public static function getSaleProducts()
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere('sale_price IS NOT NULL')
            ->andWhere('sale_price > 0')
            ->orderBy('created_at DESC')
            ->all();
    }

    /**
     * Get popular products
     */
    public static function getPopularProducts($limit = 10)
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy('id DESC') // This could be improved with actual popularity metrics
            ->limit($limit)
            ->all();
    }

    /**
     * Get effective price (sale price if available, otherwise regular price)
     */
    public function getEffectivePrice()
    {
        return $this->sale_price && $this->sale_price > 0 ? $this->sale_price : $this->price;
    }

    /**
     * Check if product is on sale
     */
    public function isOnSale()
    {
        return $this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentage()
    {
        if (!$this->isOnSale()) {
            return 0;
        }
        
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Get main image
     */
    public function getMainImage()
    {
        return $this->getImages()->one();
    }

    /**
     * Check if product is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Generate slug from name
     */
    public function generateSlug()
    {
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = $this->slugify($this->name);
        }
    }

    /**
     * Slugify string
     */
    private function slugify($text)
    {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        
        // Transliterate
        $text = iconv('utf-8', 'ascii//TRANSLIT', $text);
        
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        // Trim
        $text = trim($text, '-');
        
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        
        // Lowercase
        $text = strtolower($text);
        
        return $text;
    }

    /**
     * Before save event
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->generateSlug();
            return true;
        }
        return false;
    }
}
