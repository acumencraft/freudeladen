<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "site_settings".
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string|null $description
 * @property string $created_at
 * @property string $updated_at
 */
class SiteSetting extends ActiveRecord
{
    private static $_settings = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'site_settings';
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
            [['key'], 'required'],
            [['value'], 'string'],
            [['key'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        if (self::$_settings === null) {
            self::loadSettings();
        }

        return self::$_settings[$key] ?? $default;
    }

    /**
     * Set setting value by key
     */
    public static function set($key, $value, $description = null)
    {
        $setting = static::findOne(['key' => $key]);
        
        if (!$setting) {
            $setting = new static();
            $setting->key = $key;
            $setting->description = $description;
        }
        
        $setting->value = $value;
        
        if ($setting->save()) {
            self::$_settings[$key] = $value;
            return true;
        }
        
        return false;
    }

    /**
     * Load all settings into memory
     */
    private static function loadSettings()
    {
        self::$_settings = [];
        $settings = static::find()->all();
        
        foreach ($settings as $setting) {
            self::$_settings[$setting->key] = $setting->value;
        }
    }

    /**
     * Refresh settings cache
     */
    public static function refreshCache()
    {
        self::$_settings = null;
        self::loadSettings();
    }

    /**
     * Get multiple settings
     */
    public static function getMultiple($keys)
    {
        if (self::$_settings === null) {
            self::loadSettings();
        }

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = self::$_settings[$key] ?? null;
        }

        return $result;
    }

    /**
     * Get all settings
     */
    public static function getAll()
    {
        if (self::$_settings === null) {
            self::loadSettings();
        }

        return self::$_settings;
    }
}
