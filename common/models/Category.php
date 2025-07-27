<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $parent
 * @property Category[] $children
 * @property Product[] $products
 */
class Category extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'status'], 'integer'],
            [['name', 'slug'], 'required'],
            [['description'], 'string'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['slug'], 'match', 'pattern' => '/^[a-z0-9-]+$/', 'message' => 'Slug can only contain lowercase letters, numbers and hyphens.'],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent Category',
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['category_id' => 'id']);
    }

    /**
     * Get active categories
     */
    public static function getActiveCategories()
    {
        return static::find()->where(['status' => self::STATUS_ACTIVE])->all();
    }

    /**
     * Get category tree for dropdown
     */
    public static function getCategoryTree($parentId = null, $level = 0)
    {
        $categories = static::find()
            ->where(['parent_id' => $parentId, 'status' => self::STATUS_ACTIVE])
            ->orderBy('name')
            ->all();

        $result = [];
        foreach ($categories as $category) {
            $prefix = str_repeat('— ', $level);
            $result[$category->id] = $prefix . $category->name;
            $result = array_merge($result, static::getCategoryTree($category->id, $level + 1));
        }

        return $result;
    }

    /**
     * Get full category path
     */
    public function getFullPath()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    /**
     * Generate slug from name
     */
    public function generateSlug()
    {
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = $this->slugify($this->name);
        }
    }

    /**
     * Slugify string
     */
    private function slugify($text)
    {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        
        // Transliterate
        $text = iconv('utf-8', 'ascii//TRANSLIT', $text);
        
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        // Trim
        $text = trim($text, '-');
        
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        
        // Lowercase
        $text = strtolower($text);
        
        return $text;
    }

    /**
     * Before save event
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->generateSlug();
            return true;
        }
        return false;
    }
}
