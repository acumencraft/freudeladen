<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property string $transaction_id
 * @property int|null $order_id
 * @property int $payment_method_id
 * @property string $type
 * @property string $status
 * @property float $amount
 * @property string $currency
 * @property float|null $fee
 * @property string|null $provider_transaction_id
 * @property string|null $provider_reference
 * @property string|null $metadata
 * @property string|null $failure_reason
 * @property string|null $processed_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PaymentMethod $paymentMethod
 * @property Order $order
 */
class Transaction extends ActiveRecord
{
    // Transaction statuses
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    // Transaction types
    const TYPE_PAYMENT = 'payment';
    const TYPE_REFUND = 'refund';
    const TYPE_AUTHORIZATION = 'authorization';
    const TYPE_CAPTURE = 'capture';
    const TYPE_VOID = 'void';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
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
            [['transaction_id', 'payment_method_id', 'type', 'status', 'amount', 'currency'], 'required'],
            [['order_id', 'payment_method_id'], 'integer'],
            [['amount', 'fee'], 'number'],
            [['metadata'], 'string'],
            [['processed_at', 'created_at', 'updated_at'], 'safe'],
            [['transaction_id'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 50],
            [['currency'], 'string', 'max' => 3],
            [['provider_transaction_id', 'provider_reference'], 'string', 'max' => 255],
            [['failure_reason'], 'string', 'max' => 500],
            [['transaction_id'], 'unique'],
            [['type'], 'in', 'range' => array_keys(self::getTypeOptions())],
            [['status'], 'in', 'range' => array_keys(self::getStatusOptions())],
            [['currency'], 'default', 'value' => 'EUR'],
            [['amount'], 'number', 'min' => 0],
            [['fee'], 'number', 'min' => 0],
            [['payment_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::class, 'targetAttribute' => ['payment_method_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_id' => 'Transaction ID',
            'order_id' => 'Order ID',
            'payment_method_id' => 'Payment Method',
            'type' => 'Type',
            'status' => 'Status',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'fee' => 'Fee',
            'provider_transaction_id' => 'Provider Transaction ID',
            'provider_reference' => 'Provider Reference',
            'metadata' => 'Metadata',
            'failure_reason' => 'Failure Reason',
            'processed_at' => 'Processed At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated payment method.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::class, ['id' => 'payment_method_id']);
    }

    /**
     * Gets query for the associated order.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return array Transaction type options
     */
    public static function getTypeOptions()
    {
        return [
            self::TYPE_PAYMENT => 'Payment',
            self::TYPE_REFUND => 'Refund',
            self::TYPE_AUTHORIZATION => 'Authorization',
            self::TYPE_CAPTURE => 'Capture',
            self::TYPE_VOID => 'Void',
        ];
    }

    /**
     * @return array Transaction status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded',
            self::STATUS_PARTIALLY_REFUNDED => 'Partially Refunded',
        ];
    }

    /**
     * @return string Status label
     */
    public function getStatusLabel()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? 'Unknown';
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
     * Get metadata as array
     * @return array
     */
    public function getMetadataArray()
    {
        if (empty($this->metadata)) {
            return [];
        }
        return json_decode($this->metadata, true) ?: [];
    }

    /**
     * Set metadata from array
     * @param array $metadata
     */
    public function setMetadataArray($metadata)
    {
        $this->metadata = json_encode($metadata);
    }

    /**
     * Generate unique transaction ID
     * @return string
     */
    public static function generateTransactionId()
    {
        return 'TXN_' . date('Ymd') . '_' . strtoupper(bin2hex(random_bytes(6)));
    }

    /**
     * Mark transaction as completed
     * @param string|null $providerTransactionId
     * @param string|null $providerReference
     * @return bool
     */
    public function markAsCompleted($providerTransactionId = null, $providerReference = null)
    {
        $this->status = self::STATUS_COMPLETED;
        $this->processed_at = new Expression('NOW()');
        
        if ($providerTransactionId) {
            $this->provider_transaction_id = $providerTransactionId;
        }
        
        if ($providerReference) {
            $this->provider_reference = $providerReference;
        }
        
        return $this->save(false);
    }

    /**
     * Mark transaction as failed
     * @param string $reason
     * @return bool
     */
    public function markAsFailed($reason)
    {
        $this->status = self::STATUS_FAILED;
        $this->failure_reason = $reason;
        $this->processed_at = new Expression('NOW()');
        
        return $this->save(false);
    }

    /**
     * Check if transaction can be refunded
     * @return bool
     */
    public function canBeRefunded()
    {
        return in_array($this->status, [self::STATUS_COMPLETED]) && 
               in_array($this->type, [self::TYPE_PAYMENT, self::TYPE_CAPTURE]);
    }

    /**
     * Create refund transaction
     * @param float $amount
     * @param string $reason
     * @return Transaction|null
     */
    public function createRefund($amount, $reason = '')
    {
        if (!$this->canBeRefunded()) {
            return null;
        }
        
        if ($amount > $this->amount) {
            return null;
        }
        
        $refund = new self();
        $refund->transaction_id = self::generateTransactionId();
        $refund->order_id = $this->order_id;
        $refund->payment_method_id = $this->payment_method_id;
        $refund->type = self::TYPE_REFUND;
        $refund->status = self::STATUS_PENDING;
        $refund->amount = $amount;
        $refund->currency = $this->currency;
        $refund->setMetadataArray([
            'original_transaction_id' => $this->transaction_id,
            'refund_reason' => $reason,
        ]);
        
        if ($refund->save()) {
            // Update original transaction status if fully refunded
            if ($amount == $this->amount) {
                $this->status = self::STATUS_REFUNDED;
                $this->save(false);
            } else {
                $this->status = self::STATUS_PARTIALLY_REFUNDED;
                $this->save(false);
            }
            
            return $refund;
        }
        
        return null;
    }

    /**
     * Get transactions by status
     * @param string $status
     * @return \yii\db\ActiveQuery
     */
    public static function findByStatus($status)
    {
        return self::find()->where(['status' => $status]);
    }

    /**
     * Get transactions by type
     * @param string $type
     * @return \yii\db\ActiveQuery
     */
    public static function findByType($type)
    {
        return self::find()->where(['type' => $type]);
    }

    /**
     * Get transactions for date range
     * @param string $fromDate
     * @param string $toDate
     * @return \yii\db\ActiveQuery
     */
    public static function findByDateRange($fromDate, $toDate)
    {
        return self::find()->where(['between', 'created_at', $fromDate, $toDate]);
    }

    /**
     * Get net amount (amount - fee)
     * @return float
     */
    public function getNetAmount()
    {
        return $this->amount - ($this->fee ?: 0);
    }

    /**
     * Check if transaction is successful
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction is pending
     * @return bool
     */
    public function isPending()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if transaction has failed
     * @return bool
     */
    public function hasFailed()
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_CANCELLED]);
    }
}
