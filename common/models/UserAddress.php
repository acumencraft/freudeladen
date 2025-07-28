<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_address".
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int $is_default
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $company
 * @property string|null $address_line1
 * @property string|null $address_line2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $country
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class UserAddress extends ActiveRecord
{
    const TYPE_BILLING = 'billing';
    const TYPE_SHIPPING = 'shipping';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'required'],
            [['user_id', 'is_default'], 'integer'],
            [['type'], 'in', 'range' => [self::TYPE_BILLING, self::TYPE_SHIPPING]],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'last_name', 'company', 'address_line1', 'address_line2', 'city', 'state'], 'string', 'max' => 255],
            [['postal_code'], 'string', 'max' => 20],
            [['country'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'is_default' => 'Is Default',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'company' => 'Company',
            'address_line1' => 'Address Line 1',
            'address_line2' => 'Address Line 2',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => 'Postal Code',
            'country' => 'Country',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated user.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return array Address type options
     */
    public static function getTypeOptions()
    {
        return [
            self::TYPE_BILLING => 'Billing Address',
            self::TYPE_SHIPPING => 'Shipping Address',
        ];
    }

    /**
     * @return string Full formatted address
     */
    public function getFullAddress()
    {
        $parts = array_filter([
            $this->first_name . ' ' . $this->last_name,
            $this->company,
            $this->address_line1,
            $this->address_line2,
            $this->city . ' ' . $this->postal_code,
            $this->state,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }
}
