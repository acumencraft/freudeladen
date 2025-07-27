<?php

use yii\db\Migration;

class m250727_192245_create_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create admin user
        $this->insert('user', [
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('admin123'),
            'email' => 'admin@freudeladen.de',
            'status' => 10, // STATUS_ACTIVE
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('user', ['username' => 'admin']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250727_192245_create_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
