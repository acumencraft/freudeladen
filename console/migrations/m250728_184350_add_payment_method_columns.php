<?php

use yii\db\Migration;

class m250728_184350_add_payment_method_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add missing columns to payment_method table
        $this->addColumn('payment_method', 'fee_type', $this->string(20)->defaultValue('percentage')->after('provider'));
        $this->addColumn('payment_method', 'supported_currencies', $this->text()->null()->after('fee_percentage'));
        $this->addColumn('payment_method', 'is_active', $this->boolean()->defaultValue(1)->after('supported_currencies'));
        $this->addColumn('payment_method', 'config', $this->text()->null()->after('settings'));
        
        // Rename status column to is_active if it exists
        $tableSchema = $this->db->getTableSchema('payment_method');
        if (isset($tableSchema->columns['status'])) {
            $this->dropColumn('payment_method', 'status');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment_method', 'fee_type');
        $this->dropColumn('payment_method', 'supported_currencies');
        $this->dropColumn('payment_method', 'is_active');
        $this->dropColumn('payment_method', 'config');
        
        // Re-add status column
        $this->addColumn('payment_method', 'status', $this->tinyInteger()->defaultValue(1));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250728_184350_add_payment_method_columns cannot be reverted.\n";

        return false;
    }
    */
}
