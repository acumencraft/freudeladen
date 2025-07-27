<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "product_variants".
 *
 * @property int $id
 * @property int $product_id
 * @property string|null $sku
 * @property string $name
 * @property float $price
 * @property int $stock
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Product $product
 * @property OrderItem[] $orderItems
 * @property Cart[] $cartItems
 */
class ProductVariant extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_variants';
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
            [['product_id', 'name', 'price'], 'required'],
            [['product_id', 'stock'], 'integer'],
            [['price'], 'number', 'min' => 0],
            [['sku'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
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
            'sku' => 'SKU',
            'name' => 'Name',
            'price' => 'Price',
            'stock' => 'Stock',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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

    /**
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['variant_id' => 'id']);
    }

    /**
     * Gets query for [[CartItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCartItems()
    {
        return $this->hasMany(Cart::class, ['variant_id' => 'id']);
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }
}
