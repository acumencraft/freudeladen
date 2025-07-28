<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "faq".
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property int|null $category_id
 * @property int $sort_order
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FaqCategory $category
 */
class Faq extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faq';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question', 'answer'], 'required'],
            [['answer'], 'string'],
            [['category_id', 'sort_order', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['question'], 'string', 'max' => 500],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['sort_order'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => FaqCategory::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question' => 'Question',
            'answer' => 'Answer',
            'category_id' => 'Category',
            'sort_order' => 'Sort Order',
            'status' => 'Status',
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
        return $this->hasOne(FaqCategory::class, ['id' => 'category_id']);
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
     * @return \yii\db\ActiveQuery
     */
    public static function active()
    {
        return self::find()->where(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get FAQ items ordered by sort order
     * @return \yii\db\ActiveQuery
     */
    public static function ordered()
    {
        return self::find()->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }
}
