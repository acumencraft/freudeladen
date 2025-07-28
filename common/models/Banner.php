<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "banner".
 *
 * @property int $id
 * @property string $title
 * @property string|null $subtitle
 * @property string $image_path
 * @property string|null $link
 * @property string|null $position
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $is_active
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 */
class Banner extends ActiveRecord
{
    const POSITION_HOMEPAGE_SLIDER = 'homepage_slider';
    const POSITION_CATEGORY_TOP = 'category_top';
    const POSITION_SIDEBAR = 'sidebar';
    const POSITION_FOOTER = 'footer';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'image_path'], 'required'],
            [['subtitle'], 'string'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['is_active', 'sort_order'], 'integer'],
            [['title', 'image_path', 'link', 'position'], 'string', 'max' => 255],
            [['position'], 'in', 'range' => [
                self::POSITION_HOMEPAGE_SLIDER,
                self::POSITION_CATEGORY_TOP,
                self::POSITION_SIDEBAR,
                self::POSITION_FOOTER
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
            'title' => 'Title',
            'subtitle' => 'Subtitle',
            'image_path' => 'Image Path',
            'link' => 'Link',
            'position' => 'Position',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'is_active' => 'Is Active',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return array Position options
     */
    public static function getPositionOptions()
    {
        return [
            self::POSITION_HOMEPAGE_SLIDER => 'Homepage Slider',
            self::POSITION_CATEGORY_TOP => 'Category Top',
            self::POSITION_SIDEBAR => 'Sidebar',
            self::POSITION_FOOTER => 'Footer',
        ];
    }

    /**
     * @return string Full image URL
     */
    public function getImageUrl()
    {
        return Yii::getAlias('@web') . '/uploads/banners/' . $this->image_path;
    }

    /**
     * Check if banner is currently active based on date range
     * @return bool
     */
    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = date('Y-m-d');
        
        if ($this->start_date && $now < $this->start_date) {
            return false;
        }
        
        if ($this->end_date && $now > $this->end_date) {
            return false;
        }
        
        return true;
    }
}
