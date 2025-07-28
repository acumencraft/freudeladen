<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_history".
 *
 * @property int $id
 * @property int $order_id
 * @property string $status
 * @property string|null $comment
 * @property int|null $admin_id
 * @property string $created_at
 *
 * @property Order $order
 * @property AdminUser $admin
 */
class OrderHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'status'], 'required'],
            [['order_id', 'admin_id'], 'integer'],
            [['comment'], 'string'],
            [['created_at'], 'safe'],
            [['status'], 'string', 'max' => 50],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdminUser::class, 'targetAttribute' => ['admin_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'status' => 'Status',
            'comment' => 'Comment',
            'admin_id' => 'Admin ID',
            'created_at' => 'Created At',
        ];
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
     * Gets query for the associated admin.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(AdminUser::class, ['id' => 'admin_id']);
    }

    /**
     * @return array Available order statuses
     */
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing', 
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];
    }
}
