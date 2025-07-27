<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cart".
 *
 * @property int $id
 * @property string $session_id
 * @property int|null $user_id
 * @property int $product_id
 * @property int|null $variant_id
 * @property int $quantity
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property Product $product
 * @property ProductVariant $variant
 */
class Cart extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_id', 'product_id', 'quantity'], 'required'],
            [['user_id', 'product_id', 'variant_id', 'quantity'], 'integer'],
            [['quantity'], 'integer', 'min' => 1],
            [['session_id'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['variant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductVariant::class, 'targetAttribute' => ['variant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_id' => 'Session ID',
            'user_id' => 'User',
            'product_id' => 'Product',
            'variant_id' => 'Variant',
            'quantity' => 'Quantity',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
        return $this->hasOne(ProductVariant::class, ['id' => 'variant_id']);
    }

    /**
     * Get cart items for current session
     */
    public static function getCartItems($sessionId, $userId = null)
    {
        $query = static::find()
            ->where(['session_id' => $sessionId])
            ->with(['product.images', 'variant']);

        if ($userId) {
            $query->orWhere(['user_id' => $userId]);
        }

        return $query->all();
    }

    /**
     * Add item to cart
     */
    public static function addItem($sessionId, $productId, $quantity = 1, $variantId = null, $userId = null)
    {
        $existingItem = static::find()
            ->where([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'variant_id' => $variantId,
            ])
            ->one();

        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $result = $existingItem->save();
            if (!$result) {
                \Yii::error('Failed to update cart item: ' . json_encode($existingItem->errors), __METHOD__);
            }
            return $result;
        } else {
            $cartItem = new static();
            $cartItem->session_id = $sessionId;
            $cartItem->user_id = $userId;
            $cartItem->product_id = $productId;
            $cartItem->variant_id = $variantId;
            $cartItem->quantity = $quantity;
            $result = $cartItem->save();
            if (!$result) {
                \Yii::error('Failed to save cart item: ' . json_encode($cartItem->errors), __METHOD__);
            }
            return $result;
        }
    }

    /**
     * Get cart total
     */
    public static function getCartTotal($sessionId, $userId = null)
    {
        $items = static::getCartItems($sessionId, $userId);
        $total = 0;

        foreach ($items as $item) {
            $price = $item->variant ? $item->variant->price : $item->product->getEffectivePrice();
            $total += $price * $item->quantity;
        }

        return $total;
    }

    /**
     * Get effective price for cart item
     */
    public function getPrice()
    {
        if ($this->variant) {
            return $this->variant->price;
        }
        return $this->product->getEffectivePrice();
    }

    /**
     * Get total price for this cart item
     */
    public function getTotalPrice()
    {
        return $this->getPrice() * $this->quantity;
    }

    /**
     * Get cart items count
     */
    public static function getCartCount($sessionId, $userId = null)
    {
        $query = static::find()
            ->where(['session_id' => $sessionId]);

        if ($userId) {
            $query->orWhere(['user_id' => $userId]);
        }

        return $query->sum('quantity') ?: 0;
    }

    /**
     * Clear cart
     */
    public static function clearCart($sessionId, $userId = null)
    {
        $query = static::find()
            ->where(['session_id' => $sessionId]);

        if ($userId) {
            $query->orWhere(['user_id' => $userId]);
        }

        return static::deleteAll($query->where);
    }
}
