<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * Product model
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $short_description
 * @property string $sku
 * @property float $price
 * @property float $sale_price
 * @property integer $stock
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
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
        return '{{%products}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['category_id', 'stock', 'status'], 'integer'],
            [['description'], 'string'],
            [['price', 'sale_price'], 'number'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['sku'], 'string', 'max' => 64],
            [['short_description'], 'string', 'max' => 500],
            [['slug'], 'unique'],
            [['sku'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
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
            'description' => 'Description',
            'short_description' => 'Short Description',
            'sku' => 'SKU',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'stock' => 'Stock',
            'status' => 'Status',
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
     * Gets query for [[ProductImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductImages()
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[ProductVariants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductVariants()
    {
        return $this->hasMany(ProductVariant::class, ['product_id' => 'id']);
    }
}
