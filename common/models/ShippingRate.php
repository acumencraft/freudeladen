<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shipping_rate".
 *
 * @property int $id
 * @property string $name
 * @property int $zone_id
 * @property int $method_id
 * @property float|null $min_weight
 * @property float|null $max_weight
 * @property float|null $min_price
 * @property float|null $max_price
 * @property float $shipping_cost
 * @property float|null $free_shipping_threshold
 * @property int $status
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ShippingZone $zone
 * @property ShippingMethod $method
 */
class ShippingRate extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipping_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'zone_id', 'method_id', 'shipping_cost'], 'required'],
            [['zone_id', 'method_id', 'status', 'sort_order'], 'integer'],
            [['min_weight', 'max_weight', 'min_price', 'max_price', 'shipping_cost', 'free_shipping_threshold'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['sort_order'], 'default', 'value' => 0],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShippingZone::class, 'targetAttribute' => ['zone_id' => 'id']],
            [['method_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShippingMethod::class, 'targetAttribute' => ['method_id' => 'id']],
            // Custom validation rules
            [['max_weight'], 'compare', 'compareAttribute' => 'min_weight', 'operator' => '>=', 'when' => function($model) {
                return !empty($model->min_weight) && !empty($model->max_weight);
            }],
            [['max_price'], 'compare', 'compareAttribute' => 'min_price', 'operator' => '>=', 'when' => function($model) {
                return !empty($model->min_price) && !empty($model->max_price);
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
            'zone_id' => 'Shipping Zone',
            'method_id' => 'Shipping Method',
            'min_weight' => 'Min Weight (kg)',
            'max_weight' => 'Max Weight (kg)',
            'min_price' => 'Min Order Value (€)',
            'max_price' => 'Max Order Value (€)',
            'shipping_cost' => 'Shipping Cost (€)',
            'free_shipping_threshold' => 'Free Shipping Threshold (€)',
            'status' => 'Status',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated zone.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(ShippingZone::class, ['id' => 'zone_id']);
    }
    
    /**
     * Gets query for the associated zone (alternative name).
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShippingZone()
    {
        return $this->getZone();
    }

    /**
     * Gets query for the associated method.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMethod()
    {
        return $this->hasOne(ShippingMethod::class, ['id' => 'method_id']);
    }
    
    /**
     * Gets query for the associated method (alternative name).
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShippingMethod()
    {
        return $this->getMethod();
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
     * Check if rate applies to given weight and price
     * @param float $weight
     * @param float $price
     * @return bool
     */
    public function appliesTo($weight = null, $price = null)
    {
        // Check weight constraints
        if ($weight !== null) {
            if ($this->min_weight !== null && $weight < $this->min_weight) {
                return false;
            }
            if ($this->max_weight !== null && $weight > $this->max_weight) {
                return false;
            }
        }

        // Check price constraints
        if ($price !== null) {
            if ($this->min_price !== null && $price < $this->min_price) {
                return false;
            }
            if ($this->max_price !== null && $price > $this->max_price) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate final shipping cost considering free shipping threshold
     * @param float $orderValue
     * @return float
     */
    public function calculateCost($orderValue = 0)
    {
        // Check if free shipping applies
        if ($this->free_shipping_threshold !== null && $orderValue >= $this->free_shipping_threshold) {
            return 0;
        }

        return $this->shipping_cost;
    }

    /**
     * Get formatted weight range
     * @return string
     */
    public function getWeightRange()
    {
        if ($this->min_weight === null && $this->max_weight === null) {
            return 'Any weight';
        }

        if ($this->min_weight !== null && $this->max_weight !== null) {
            return $this->min_weight . ' - ' . $this->max_weight . ' kg';
        }

        if ($this->min_weight !== null) {
            return 'From ' . $this->min_weight . ' kg';
        }

        return 'Up to ' . $this->max_weight . ' kg';
    }

    /**
     * Get formatted price range
     * @return string
     */
    public function getPriceRange()
    {
        if ($this->min_price === null && $this->max_price === null) {
            return 'Any order value';
        }

        if ($this->min_price !== null && $this->max_price !== null) {
            return '€' . $this->min_price . ' - €' . $this->max_price;
        }

        if ($this->min_price !== null) {
            return 'From €' . $this->min_price;
        }

        return 'Up to €' . $this->max_price;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function active()
    {
        return self::find()->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Find applicable rates for zone, weight and price
     * @param int $zoneId
     * @param float|null $weight
     * @param float|null $price
     * @return \yii\db\ActiveQuery
     */
    public static function findApplicable($zoneId, $weight = null, $price = null)
    {
        $query = self::active()->where(['zone_id' => $zoneId]);

        if ($weight !== null) {
            $query->andWhere([
                'or',
                ['min_weight' => null],
                ['<=', 'min_weight', $weight]
            ])->andWhere([
                'or',
                ['max_weight' => null],
                ['>=', 'max_weight', $weight]
            ]);
        }

        if ($price !== null) {
            $query->andWhere([
                'or',
                ['min_price' => null],
                ['<=', 'min_price', $price]
            ])->andWhere([
                'or',
                ['max_price' => null],
                ['>=', 'max_price', $price]
            ]);
        }

        return $query;
    }
}
