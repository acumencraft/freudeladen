<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_images".
 *
 * @property int $id
 * @property int $product_id
 * @property string $image_url
 * @property string|null $alt_text
 * @property int $sort_order
 * @property string $created_at
 *
 * @property Product $product
 */
class ProductImage extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'image_url'], 'required'],
            [['product_id', 'sort_order'], 'integer'],
            [['image_url'], 'string', 'max' => 500],
            [['alt_text'], 'string', 'max' => 255],
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
            'product_id' => 'Product',
            'image_url' => 'Image URL',
            'alt_text' => 'Alt Text',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }
}
