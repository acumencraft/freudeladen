<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Order model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $status
 * @property float $total
 * @property string $payment_method
 * @property string $payment_status
 * @property string $shipping_address
 * @property string $billing_address
 * @property string $created_at
 * @property string $updated_at
 */
class Order extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
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
            [['total'], 'required'],
            [['user_id'], 'integer'],
            [['total'], 'number'],
            [['shipping_address', 'billing_address'], 'string'],
            [['status', 'payment_method', 'payment_status'], 'string', 'max' => 50],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['payment_status', 'default', 'value' => self::PAYMENT_PENDING],
            ['status', 'in', 'range' => [
                self::STATUS_PENDING, 
                self::STATUS_PROCESSING, 
                self::STATUS_SHIPPED, 
                self::STATUS_DELIVERED, 
                self::STATUS_CANCELLED,
                self::STATUS_COMPLETED
            ]],
            ['payment_status', 'in', 'range' => [
                self::PAYMENT_PENDING, 
                self::PAYMENT_PAID, 
                self::PAYMENT_FAILED, 
                self::PAYMENT_REFUNDED
            ]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'status' => 'Status',
            'total' => 'Total',
            'payment_method' => 'Payment Method',
            'payment_status' => 'Payment Status',
            'shipping_address' => 'Shipping Address',
            'billing_address' => 'Billing Address',
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
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }
}
