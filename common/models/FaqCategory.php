<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "faq_category".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $sort_order
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Faq[] $faqs
 */
class FaqCategory extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faq_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['description'], 'string'],
            [['sort_order', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
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
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'sort_order' => 'Sort Order',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated FAQs.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaqs()
    {
        return $this->hasMany(Faq::class, ['category_id' => 'id']);
    }

    /**
     * Generate slug from name
     * @param string $name
     * @return string
     */
    public static function generateSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        // Check for uniqueness
        $counter = 1;
        $originalSlug = $slug;
        while (self::find()->where(['slug' => $slug])->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
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
     * @return int Number of FAQs in this category
     */
    public function getFaqCount()
    {
        return $this->getFaqs()->count();
    }

    /**
     * Get active FAQ count for this category
     * @return int
     */
    public function getActiveFaqCount()
    {
        return $this->getFaqs()->where(['status' => Faq::STATUS_ACTIVE])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function active()
    {
        return self::find()->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get categories ordered by sort order
     * @return \yii\db\ActiveQuery
     */
    public static function ordered()
    {
        return self::find()->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC]);
    }
}
