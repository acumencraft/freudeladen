<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $order_number
 * @property string $status
 * @property string $payment_status
 * @property string|null $payment_method
 * @property float $total_amount
 * @property float|null $tax_amount
 * @property float|null $shipping_amount
 * @property float|null $discount_amount
 * @property string|null $currency
 * @property string $customer_email
 * @property string|null $customer_phone
 * @property string|null $customer_notes
 * @property string|null $shipping_first_name
 * @property string|null $shipping_last_name
 * @property string|null $shipping_company
 * @property string|null $shipping_address_1
 * @property string|null $shipping_address_2
 * @property string|null $shipping_city
 * @property string|null $shipping_state
 * @property string|null $shipping_postal_code
 * @property string|null $shipping_country
 * @property string|null $billing_first_name
 * @property string|null $billing_last_name
 * @property string|null $billing_company
 * @property string|null $billing_address_1
 * @property string|null $billing_address_2
 * @property string|null $billing_city
 * @property string|null $billing_state
 * @property string|null $billing_postal_code
 * @property string|null $billing_country
 * @property string|null $payment_transaction_id
 * @property string|null $payment_data
 * @property string|null $ordered_at
 * @property string|null $shipped_at
 * @property string|null $delivered_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property OrderItem[] $orderItems
 */
class Order extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    const PAYMENT_METHOD_STRIPE = 'stripe';
    const PAYMENT_METHOD_PAYPAL = 'paypal';
    const PAYMENT_METHOD_CRYPTO = 'crypto';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
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
            [['customer_email', 'total_amount'], 'required'],
            [['user_id'], 'integer'],
            [['total_amount', 'tax_amount', 'shipping_amount', 'discount_amount'], 'number', 'min' => 0],
            [['customer_notes', 'payment_data'], 'string'],
            [['order_number'], 'string', 'max' => 50],
            [['order_number'], 'unique'],
            [['status', 'payment_status', 'payment_method'], 'string', 'max' => 50],
            [['currency'], 'string', 'max' => 3],
            [['customer_email'], 'email'],
            [['customer_email'], 'string', 'max' => 255],
            [['customer_phone'], 'string', 'max' => 20],
            [['shipping_first_name', 'shipping_last_name', 'shipping_company', 
              'shipping_address_1', 'shipping_address_2', 'shipping_city', 
              'shipping_state', 'billing_first_name', 'billing_last_name', 
              'billing_company', 'billing_address_1', 'billing_address_2', 
              'billing_city', 'billing_state', 'payment_transaction_id'], 'string', 'max' => 255],
            [['shipping_postal_code', 'billing_postal_code'], 'string', 'max' => 20],
            [['shipping_country', 'billing_country'], 'string', 'max' => 2],
            [['status'], 'in', 'range' => [
                self::STATUS_PENDING,
                self::STATUS_CONFIRMED,
                self::STATUS_PROCESSING,
                self::STATUS_SHIPPED,
                self::STATUS_DELIVERED,
                self::STATUS_CANCELLED,
            ]],
            [['payment_status'], 'in', 'range' => [
                self::PAYMENT_STATUS_PENDING,
                self::PAYMENT_STATUS_PAID,
                self::PAYMENT_STATUS_FAILED,
                self::PAYMENT_STATUS_REFUNDED,
            ]],
            [['payment_method'], 'in', 'range' => [
                self::PAYMENT_METHOD_STRIPE,
                self::PAYMENT_METHOD_PAYPAL,
                self::PAYMENT_METHOD_CRYPTO,
                self::PAYMENT_METHOD_BANK_TRANSFER,
            ]],
            // Virtual property validation
            [['customer_name'], 'string', 'max' => 255],
            [['customer_name'], 'required'],
            [['customer_name'], 'safe'],
            [['shipping_address', 'billing_address'], 'string'],
            [['shipping_address'], 'required'],
            [['shipping_address', 'billing_address'], 'safe'],
            [['notes'], 'string'],
            [['notes'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * Get next order number
     */
    public function getNextOrderNumber()
    {
        $todayPrefix = date('Ymd');
        $lastOrder = static::find()
            ->where(['like', 'order_number', 'ORD-' . $todayPrefix])
            ->orderBy(['id' => SORT_DESC])
            ->one();
            
        if ($lastOrder) {
            // Extract number from order_number (e.g., ORD-20250728-0001 -> 1)
            $parts = explode('-', $lastOrder->order_number);
            $lastNumber = isset($parts[2]) ? intval($parts[2]) : 0;
            return $lastNumber + 1;
        }
        
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Order ID',
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

        /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        if (in_array($name, ['customer_name', 'shipping_address', 'billing_address', 'notes'])) {
            // Convert snake_case to camelCase for method names
            $camelName = str_replace('_', '', ucwords($name, '_'));
            $setterMethod = 'set' . $camelName;
            if (method_exists($this, $setterMethod)) {
                $this->$setterMethod($value);
                return;
            }
        }
        parent::__set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        if (in_array($name, ['customer_name', 'shipping_address', 'billing_address', 'notes'])) {
            // Convert snake_case to camelCase for method names
            $camelName = str_replace('_', '', ucwords($name, '_'));
            $getterMethod = 'get' . $camelName;
            if (method_exists($this, $getterMethod)) {
                return $this->$getterMethod();
            }
        }
        return parent::__get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (in_array($name, ['customer_name', 'shipping_address', 'billing_address', 'notes'])) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * {@inheritdoc}
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        if (in_array($name, ['customer_name', 'shipping_address', 'billing_address', 'notes'])) {
            return true;
        }
        return parent::canSetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * Virtual property getter for customer_name
     */
    public function getCustomerName()
    {
        return trim(($this->shipping_first_name ?? '') . ' ' . ($this->shipping_last_name ?? ''));
    }

    /**
     * Virtual property setter for customer_name
     */
    public function setCustomerName($value)
    {
        $parts = explode(' ', trim($value), 2);
        $this->shipping_first_name = $parts[0] ?? '';
        $this->shipping_last_name = $parts[1] ?? '';
        
        // Also set billing if not set
        if (empty($this->billing_first_name)) {
            $this->billing_first_name = $this->shipping_first_name;
        }
        if (empty($this->billing_last_name)) {
            $this->billing_last_name = $this->shipping_last_name;
        }
    }

    /**
     * Virtual property getter for shipping_address
     */
    public function getShippingAddress()
    {
        $parts = array_filter([
            $this->shipping_address_1,
            $this->shipping_address_2,
            $this->shipping_city . ' ' . $this->shipping_postal_code,
            $this->shipping_country
        ]);
        return implode("
", $parts);
    }

    /**
     * Virtual property setter for shipping_address
     */
    public function setShippingAddress($value)
    {
        $lines = explode("
", trim($value));
        $this->shipping_address_1 = $lines[0] ?? '';
        $this->shipping_address_2 = $lines[1] ?? '';
        
        // Try to parse city and postal code from third line
        if (isset($lines[2])) {
            $cityPostal = trim($lines[2]);
            if (preg_match('/^(.+?)\s+(\d{5})$/', $cityPostal, $matches)) {
                $this->shipping_postal_code = $matches[2];
                $this->shipping_city = $matches[1];
            } else {
                $this->shipping_city = $cityPostal;
            }
        }
        
        $this->shipping_country = $lines[3] ?? 'DE';
    }

    /**
     * Virtual property getter for billing_address
     */
    public function getBillingAddress()
    {
        $parts = array_filter([
            $this->billing_address_1,
            $this->billing_address_2,
            $this->billing_city . ' ' . $this->billing_postal_code,
            $this->billing_country
        ]);
        return implode("
", $parts);
    }

    /**
     * Virtual property setter for billing_address
     */
    public function setBillingAddress($value)
    {
        $lines = explode("
", trim($value));
        $this->billing_address_1 = $lines[0] ?? '';
        $this->billing_address_2 = $lines[1] ?? '';
        
        // Try to parse city and postal code from third line
        if (isset($lines[2])) {
            $cityPostal = trim($lines[2]);
            if (preg_match('/^(.+?)\s+(\d{5})$/', $cityPostal, $matches)) {
                $this->billing_postal_code = $matches[2];
                $this->billing_city = $matches[1];
            } else {
                $this->billing_city = $cityPostal;
            }
        }
        
        $this->billing_country = $lines[3] ?? 'DE';
    }

    /**
     * Virtual property getter for notes
     */
    public function getNotes()
    {
        return $this->customer_notes;
    }

    /**
     * Virtual property setter for notes
     */
    public function setNotes($value)
    {
        $this->customer_notes = $value;
    }

    /**
     * Get status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get payment status options
     */
    public static function getPaymentStatusOptions()
    {
        return [
            self::PAYMENT_STATUS_PENDING => 'Pending',
            self::PAYMENT_STATUS_PAID => 'Paid',
            self::PAYMENT_STATUS_FAILED => 'Failed',
            self::PAYMENT_STATUS_REFUNDED => 'Refunded',
        ];
    }

    /**
     * Get payment method options
     */
    public static function getPaymentMethodOptions()
    {
        return [
            self::PAYMENT_METHOD_STRIPE => 'Credit/Debit Card',
            self::PAYMENT_METHOD_PAYPAL => 'PayPal',
            self::PAYMENT_METHOD_CRYPTO => 'Cryptocurrency',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Bank Transfer',
        ];
    }

    /**
     * Create order from cart
     */
    public static function createFromCart($cartItems, $userData = [])
    {
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $order = new static();
            $order->user_id = $userData['user_id'] ?? null;
            $order->shipping_address = $userData['shipping_address'] ?? null;
            $order->billing_address = $userData['billing_address'] ?? null;
            $order->payment_method = $userData['payment_method'] ?? null;
            $order->status = self::STATUS_PENDING;
            $order->payment_status = self::PAYMENT_STATUS_PENDING;
            
            $total = 0;
            
            if (!$order->save()) {
                throw new \Exception('Failed to create order');
            }
            
            foreach ($cartItems as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $cartItem->product_id;
                $orderItem->variant_id = $cartItem->variant_id;
                $orderItem->quantity = $cartItem->quantity;
                
                $price = $cartItem->variant ? $cartItem->variant->price : $cartItem->product->getEffectivePrice();
                $orderItem->price = $price;
                $total += $price * $cartItem->quantity;
                
                if (!$orderItem->save()) {
                    throw new \Exception('Failed to create order item');
                }
            }
            
            $order->total = $total;
            if (!$order->save()) {
                throw new \Exception('Failed to update order total');
            }
            
            $transaction->commit();
            return $order;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Get order total items count
     */
    public function getItemsCount()
    {
        return $this->getOrderItems()->sum('quantity') ?: 0;
    }
}
