<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shipping_zone".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $countries
 * @property int $status
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ShippingRate[] $shippingRates
 */
class ShippingZone extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipping_zone';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'countries'], 'required'],
            [['description'], 'string'],
            [['status', 'sort_order'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['countries'], 'string'],
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
            'name' => 'Zone Name',
            'description' => 'Description',
            'countries' => 'Countries',
            'status' => 'Status',
            'sort_order' => 'Sort Order',
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
        return $this->hasMany(ShippingRate::class, ['zone_id' => 'id']);
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
     * @return string Status label
     */
    public function getStatusLabel()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? 'Unknown';
    }

    /**
     * Get countries as array
     * @return array
     */
    public function getCountriesArray()
    {
        if (empty($this->countries)) {
            return [];
        }
        return explode(',', $this->countries);
    }

    /**
     * Set countries from array
     * @param array $countries
     */
    public function setCountriesArray($countries)
    {
        $this->countries = is_array($countries) ? implode(',', $countries) : $countries;
    }

    /**
     * Check if zone covers specific country
     * @param string $countryCode
     * @return bool
     */
    public function coversCountry($countryCode)
    {
        return in_array($countryCode, $this->getCountriesArray());
    }

    /**
     * Get formatted countries list
     * @return string
     */
    public function getFormattedCountries()
    {
        $countries = $this->getCountriesArray();
        if (empty($countries)) {
            return 'No countries';
        }
        
        // Simple country code to name mapping (extend as needed)
        $countryNames = [
            'DE' => 'Germany',
            'AT' => 'Austria',
            'CH' => 'Switzerland',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'PL' => 'Poland',
            'CZ' => 'Czech Republic',
        ];
        
        $names = [];
        foreach ($countries as $code) {
            $names[] = $countryNames[$code] ?? $code;
        }
        
        return implode(', ', $names);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function active()
    {
        return self::find()->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get zones ordered by sort order
     * @return \yii\db\ActiveQuery
     */
    public static function ordered()
    {
        return self::find()->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC]);
    }

    /**
     * Get shipping rates count for this zone
     * @return int
     */
    public function getRatesCount()
    {
        return $this->getShippingRates()->count();
    }
}
