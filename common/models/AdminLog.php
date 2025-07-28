<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * AdminLog model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $action
 * @property string $object_type
 * @property integer $object_id
 * @property string $details
 * @property string $ip_address
 * @property string $created_at
 *
 * @property AdminUser $user
 */
class AdminLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action'], 'required'],
            [['user_id', 'object_id'], 'integer'],
            [['details'], 'string'],
            [['action', 'object_type'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 45],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Benutzer ID',
            'action' => 'Aktion',
            'object_type' => 'Objekttyp',
            'object_id' => 'Objekt ID',
            'details' => 'Details',
            'ip_address' => 'IP-Adresse',
            'created_at' => 'Erstellt am',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(AdminUser::class, ['id' => 'user_id']);
    }

    /**
     * Static method to log admin actions
     *
     * @param string $action
     * @param string|null $objectType
     * @param integer|null $objectId
     * @param string|null $details
     * @param integer|null $userId
     * @return bool
     */
    public static function log($action, $objectType = null, $objectId = null, $details = null, $userId = null)
    {
        $log = new static();
        $log->user_id = $userId ?: (Yii::$app->user->isGuest ? null : Yii::$app->user->id);
        $log->action = $action;
        $log->object_type = $objectType;
        $log->object_id = $objectId;
        $log->details = $details;
        $log->ip_address = Yii::$app->request->userIP;
        
        return $log->save();
    }

    /**
     * Get formatted action text
     *
     * @return string
     */
    public function getFormattedAction()
    {
        $actions = [
            'login' => 'Anmeldung',
            'logout' => 'Abmeldung',
            'create' => 'Erstellt',
            'update' => 'Aktualisiert',
            'delete' => 'Gelöscht',
            'view' => 'Angesehen',
            'export' => 'Exportiert',
            'import' => 'Importiert',
        ];

        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Get formatted object description
     *
     * @return string
     */
    public function getObjectDescription()
    {
        if (!$this->object_type) {
            return '';
        }

        $types = [
            'product' => 'Produkt',
            'order' => 'Bestellung',
            'user' => 'Benutzer',
            'admin_user' => 'Admin-Benutzer',
            'category' => 'Kategorie',
            'page' => 'Seite',
            'banner' => 'Banner',
            'setting' => 'Einstellung',
        ];

        $typeName = $types[$this->object_type] ?? $this->object_type;
        
        if ($this->object_id) {
            return $typeName . ' #' . $this->object_id;
        }
        
        return $typeName;
    }

    /**
     * Get user display name
     *
     * @return string
     */
    public function getUserDisplayName()
    {
        return $this->user ? $this->user->getDisplayName() : 'System';
    }
}
