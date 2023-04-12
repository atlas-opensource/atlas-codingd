<?php

use yii\db\Migration;

/**
 * Class m190113_213542_create_role
 */
class m190113_213542_create_role extends Migration
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
        echo "m190113_213542_create_role cannot be reverted.\n";
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190113_213542_create_role cannot be reverted.\n";

        return false;
    }
    */
}
