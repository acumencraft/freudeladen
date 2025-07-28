<?php

use yii\db\Migration;

class m250728_154756_add_blog_and_faq_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250728_154756_add_blog_and_faq_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250728_154756_add_blog_and_faq_tables cannot be reverted.\n";

        return false;
    }
    */
}
