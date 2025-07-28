<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_seo".
 *
 * @property int $id
 * @property int $product_id
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string $slug
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Product $product
 */
class ProductSeo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_seo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'slug'], 'required'],
            [['product_id'], 'integer'],
            [['meta_description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['meta_title', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['product_id'], 'unique'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'slug' => 'Slug',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated product.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Generate slug from product name
     * @param string $name
     * @return string
     */
    public static function generateSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        // Check for uniqueness
        $counter = 1;
        $originalSlug = $slug;
        while (self::find()->where(['slug' => $slug])->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
