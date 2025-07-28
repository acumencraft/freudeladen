<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "page".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int $is_in_menu
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Page extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug'], 'required'],
            [['content', 'meta_description'], 'string'],
            [['is_in_menu', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'slug', 'meta_title'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
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
            'slug' => 'Slug',
            'content' => 'Content',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'is_in_menu' => 'Show in Menu',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return array Status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
        ];
    }

    /**
     * Generate slug from title
     * @param string $title
     * @return string
     */
    public static function generateSlug($title)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
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
     * @return string Status label
     */
    public function getStatusLabel()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? 'Unknown';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function published()
    {
        return self::find()->where(['status' => self::STATUS_PUBLISHED]);
    }
}
