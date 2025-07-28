<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "payment_method".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string $type
 * @property string|null $provider
 * @property string|null $fee_type
 * @property string|null $settings
 * @property string|null $config
 * @property float|null $min_amount
 * @property float|null $max_amount
 * @property float|null $fee_fixed
 * @property float|null $fee_percentage
 * @property string|null $supported_currencies
 * @property int $is_active
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Transaction[] $transactions
 */
class PaymentMethod extends ActiveRecord
{
    // Payment types
    const TYPE_CREDIT_CARD = 'credit_card';
    const TYPE_DEBIT_CARD = 'debit_card';
    const TYPE_PAYPAL = 'paypal';
    const TYPE_BANK_TRANSFER = 'bank_transfer';
    const TYPE_SEPA = 'sepa';
    const TYPE_INVOICE = 'invoice';
    const TYPE_CASH_ON_DELIVERY = 'cod';
    const TYPE_SOFORT = 'sofort';
    const TYPE_GIROPAY = 'giropay';
    const TYPE_KLARNA = 'klarna';
    const TYPE_APPLE_PAY = 'apple_pay';
    const TYPE_GOOGLE_PAY = 'google_pay';

    // Payment providers
    const PROVIDER_STRIPE = 'stripe';
    const PROVIDER_PAYPAL = 'paypal';
    const PROVIDER_MOLLIE = 'mollie';
    const PROVIDER_ADYEN = 'adyen';
    const PROVIDER_KLARNA = 'klarna';
    const PROVIDER_SQUARE = 'square';
    const PROVIDER_CUSTOM = 'custom';

    // Fee types
    const FEE_TYPE_FIXED = 'fixed';
    const FEE_TYPE_PERCENTAGE = 'percentage';
    const FEE_TYPE_BOTH = 'both';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_method';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'type'], 'required'],
            [['description', 'settings', 'config', 'supported_currencies'], 'string'],
            [['min_amount', 'max_amount', 'fee_fixed', 'fee_percentage'], 'number'],
            [['is_active', 'sort_order'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 100],
            [['type'], 'string', 'max' => 50],
            [['provider'], 'string', 'max' => 100],
            [['fee_type'], 'string', 'max' => 20],
            [['code'], 'unique'],
            [['is_active'], 'boolean'],
            [['is_active'], 'default', 'value' => 1],
            [['sort_order'], 'default', 'value' => 0],
            [['type'], 'in', 'range' => array_keys(self::getTypeOptions())],
            [['provider'], 'in', 'range' => array_keys(self::getProviderOptions())],
            [['fee_type'], 'in', 'range' => array_keys(self::getFeeTypeOptions())],
            [['fee_percentage'], 'number', 'min' => 0, 'max' => 100],
            [['min_amount', 'max_amount', 'fee_fixed'], 'number', 'min' => 0],
            [['max_amount'], 'compare', 'compareAttribute' => 'min_amount', 'operator' => '>=', 'when' => function($model) {
                return !empty($model->min_amount) && !empty($model->max_amount);
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'description' => 'Description',
            'type' => 'Payment Type',
            'provider' => 'Payment Provider',
            'fee_type' => 'Fee Type',
            'settings' => 'Settings',
            'config' => 'Configuration',
            'min_amount' => 'Minimum Amount (€)',
            'max_amount' => 'Maximum Amount (€)',
            'fee_fixed' => 'Fixed Fee (€)',
            'fee_percentage' => 'Percentage Fee (%)',
            'supported_currencies' => 'Supported Currencies',
            'is_active' => 'Active',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated transactions.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['payment_method_id' => 'id']);
    }

    /**
     * @return array Payment type options
     */
    public static function getTypeOptions()
    {
        return [
            self::TYPE_CREDIT_CARD => 'Credit Card',
            self::TYPE_DEBIT_CARD => 'Debit Card',
            self::TYPE_PAYPAL => 'PayPal',
            self::TYPE_BANK_TRANSFER => 'Bank Transfer',
            self::TYPE_SEPA => 'SEPA Direct Debit',
            self::TYPE_INVOICE => 'Invoice',
            self::TYPE_CASH_ON_DELIVERY => 'Cash on Delivery',
            self::TYPE_SOFORT => 'Sofort Banking',
            self::TYPE_GIROPAY => 'Giropay',
            self::TYPE_KLARNA => 'Klarna',
            self::TYPE_APPLE_PAY => 'Apple Pay',
            self::TYPE_GOOGLE_PAY => 'Google Pay',
        ];
    }

    /**
     * @return array Payment provider options
     */
    public static function getProviderOptions()
    {
        return [
            self::PROVIDER_STRIPE => 'Stripe',
            self::PROVIDER_PAYPAL => 'PayPal',
            self::PROVIDER_MOLLIE => 'Mollie',
            self::PROVIDER_ADYEN => 'Adyen',
            self::PROVIDER_KLARNA => 'Klarna',
            self::PROVIDER_SQUARE => 'Square',
            self::PROVIDER_CUSTOM => 'Custom',
        ];
    }

    /**
     * @return array Fee type options
     */
    public static function getFeeTypeOptions()
    {
        return [
            self::FEE_TYPE_FIXED => 'Fixed Amount',
            self::FEE_TYPE_PERCENTAGE => 'Percentage',
            self::FEE_TYPE_BOTH => 'Fixed + Percentage',
        ];
    }

    /**
     * @return string Type label
     */
    public function getTypeLabel()
    {
        $options = self::getTypeOptions();
        return $options[$this->type] ?? $this->type;
    }

    /**
     * @return string Provider label
     */
    public function getProviderLabel()
    {
        $options = self::getProviderOptions();
        return $options[$this->provider] ?? $this->provider;
    }

    /**
     * Get settings as array
     * @return array
     */
    public function getSettingsArray()
    {
        if (empty($this->settings)) {
            return [];
        }
        return json_decode($this->settings, true) ?: [];
    }

    /**
     * Set settings from array
     * @param array $settings
     */
    public function setSettingsArray($settings)
    {
        $this->settings = json_encode($settings);
    }

    /**
     * Get config as array
     * @return array
     */
    public function getConfigArray()
    {
        if (empty($this->config)) {
            return [];
        }
        return json_decode($this->config, true) ?: [];
    }

    /**
     * Set config from array
     * @param array $config
     */
    public function setConfigArray($config)
    {
        $this->config = json_encode($config);
    }

    /**
     * Get supported currencies as array
     * @return array
     */
    public function getSupportedCurrenciesArray()
    {
        if (empty($this->supported_currencies)) {
            return [];
        }
        return array_map('trim', explode(',', $this->supported_currencies));
    }

    /**
     * Set supported currencies from array
     * @param array $currencies
     */
    public function setSupportedCurrenciesArray($currencies)
    {
        $this->supported_currencies = implode(',', array_filter($currencies));
    }

    /**
     * Get fee type label
     * @return string
     */
    public function getFeeTypeLabel()
    {
        $options = self::getFeeTypeOptions();
        return $options[$this->fee_type] ?? $this->fee_type;
    }

    /**
     * Calculate total fee for amount
     * @param float $amount
     * @return float
     */
    public function calculateFee($amount)
    {
        $fee = 0;
        
        if ($this->fee_fixed) {
            $fee += $this->fee_fixed;
        }
        
        if ($this->fee_percentage) {
            $fee += ($amount * $this->fee_percentage / 100);
        }
        
        return round($fee, 2);
    }

    /**
     * Check if payment method is available for amount
     * @param float $amount
     * @return bool
     */
    public function isAvailableForAmount($amount)
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->min_amount && $amount < $this->min_amount) {
            return false;
        }
        
        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }
        
        return true;
    }

    /**
     * Get count of transactions for this payment method
     * @return int
     */
    public function getTransactionCount()
    {
        return $this->getTransactions()->count();
    }

    /**
     * Get total amount processed by this payment method
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->getTransactions()
            ->where(['status' => Transaction::STATUS_COMPLETED])
            ->sum('amount') ?: 0;
    }

    /**
     * Get active payment methods
     * @return \yii\db\ActiveQuery
     */
    public static function getActive()
    {
        return self::find()->where(['is_active' => 1])->orderBy(['sort_order' => SORT_ASC]);
    }

    /**
     * Get available payment methods for amount
     * @param float $amount
     * @return PaymentMethod[]
     */
    public static function getAvailableForAmount($amount)
    {
        $methods = self::getActive()->all();
        $available = [];
        
        foreach ($methods as $method) {
            if ($method->isAvailableForAmount($amount)) {
                $available[] = $method;
            }
        }
        
        return $available;
    }
}
