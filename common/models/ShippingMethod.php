<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shipping_method".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string|null $provider
 * @property int $status
 * @property int $sort_order
 * @property string|null $settings
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ShippingRate[] $shippingRates
 */
class ShippingMethod extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    // Common shipping providers
    const PROVIDER_DHL = 'dhl';
    const PROVIDER_DPD = 'dpd';
    const PROVIDER_HERMES = 'hermes';
    const PROVIDER_UPS = 'ups';
    const PROVIDER_FEDEX = 'fedex';
    const PROVIDER_CUSTOM = 'custom';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipping_method';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['description', 'settings'], 'string'],
            [['status', 'sort_order'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'code', 'provider'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['sort_order'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Method Name',
            'code' => 'Method Code',
            'description' => 'Description',
            'provider' => 'Provider',
            'status' => 'Status',
            'sort_order' => 'Sort Order',
            'settings' => 'Settings (JSON)',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated shipping rates.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShippingRates()
    {
        return $this->hasMany(ShippingRate::class, ['method_id' => 'id']);
    }

    /**
     * @return array Status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    /**
     * @return array Provider options
     */
    public static function getProviderOptions()
    {
        return [
            self::PROVIDER_DHL => 'DHL',
            self::PROVIDER_DPD => 'DPD',
            self::PROVIDER_HERMES => 'Hermes',
            self::PROVIDER_UPS => 'UPS',
            self::PROVIDER_FEDEX => 'FedEx',
            self::PROVIDER_CUSTOM => 'Custom',
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
        $this->settings = is_array($settings) ? json_encode($settings) : $settings;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function active()
    {
        return self::find()->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get methods ordered by sort order
     * @return \yii\db\ActiveQuery
     */
    public static function ordered()
    {
        return self::find()->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC]);
    }

    /**
     * Get shipping rates count for this method
     * @return int
     */
    public function getRatesCount()
    {
        return $this->getShippingRates()->count();
    }
}
