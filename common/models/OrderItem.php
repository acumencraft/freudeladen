<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_items".
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property string $product_name
 * @property string|null $product_sku
 * @property int $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property string|null $product_data
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Order $order
 * @property Product $product
 * @property ProductVariant $variant
 */
class OrderItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price'], 'required'],
            [['order_id', 'product_id', 'product_variant_id', 'quantity'], 'integer'],
            [['unit_price', 'total_price'], 'number', 'min' => 0],
            [['quantity'], 'integer', 'min' => 1],
            [['product_name'], 'string', 'max' => 255],
            [['product_sku'], 'string', 'max' => 50],
            [['product_data'], 'string'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['product_variant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductVariant::class, 'targetAttribute' => ['product_variant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order',
            'product_id' => 'Product',
            'product_variant_id' => 'Variant',
            'product_name' => 'Product Name',
            'product_sku' => 'SKU',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'total_price' => 'Total Price',
            'product_data' => 'Product Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
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
     * Gets query for [[Variant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVariant()
    {
        return $this->hasOne(ProductVariant::class, ['id' => 'product_variant_id']);
    }

    /**
     * Get item total price (backward compatibility)
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * Get price (backward compatibility)
     */
    public function getPrice()
    {
        return $this->unit_price;
    }
}
