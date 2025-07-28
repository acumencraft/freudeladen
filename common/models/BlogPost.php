<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "blog_post".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $excerpt
 * @property int|null $category_id
 * @property string|null $featured_image
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int $status
 * @property string|null $published_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BlogCategory $category
 * @property BlogTag[] $tags
 */
class BlogPost extends ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog_post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug'], 'required'],
            [['content', 'excerpt', 'meta_description'], 'string'],
            [['category_id', 'status'], 'integer'],
            [['published_at', 'created_at', 'updated_at'], 'safe'],
            [['title', 'slug', 'featured_image', 'meta_title'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => BlogCategory::class, 'targetAttribute' => ['category_id' => 'id']],
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
            'excerpt' => 'Excerpt',
            'category_id' => 'Category',
            'featured_image' => 'Featured Image',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'status' => 'Status',
            'published_at' => 'Published At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for the associated category.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(BlogCategory::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for the associated tags.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(BlogTag::class, ['id' => 'tag_id'])
            ->viaTable('blog_post_tag', ['post_id' => 'id']);
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

    /**
     * @return string Full featured image URL
     */
    public function getFeaturedImageUrl()
    {
        if ($this->featured_image) {
            return Yii::getAlias('@web') . '/uploads/blog/' . $this->featured_image;
        }
        return null;
    }

    /**
     * Get excerpt with fallback to content
     * @param int $length
     * @return string
     */
    public function getExcerptText($length = 200)
    {
        if (!empty($this->excerpt)) {
            return $this->excerpt;
        }
        
        $content = strip_tags($this->content);
        if (strlen($content) > $length) {
            return substr($content, 0, $length) . '...';
        }
        
        return $content;
    }
}
