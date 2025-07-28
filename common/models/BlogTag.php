<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "blog_tag".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BlogPost[] $posts
 */
class BlogTag extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['description', 'meta_description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'slug', 'meta_title'], 'string', 'max' => 255],
            [['slug'], 'unique'],
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
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated posts.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(BlogPost::class, ['id' => 'post_id'])
            ->viaTable('blog_post_tag', ['tag_id' => 'id']);
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
     * Get posts count for this tag
     * @return int
     */
    public function getPostCount()
    {
        return $this->getPosts()->count();
    }

    /**
     * Get published posts count for this tag
     * @return int
     */
    public function getPublishedPostCount()
    {
        return $this->getPosts()
            ->where(['status' => BlogPost::STATUS_PUBLISHED])
            ->count();
    }
}
