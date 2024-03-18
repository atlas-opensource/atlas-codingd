<?php

use yii\db\Migration;

/**
 * Class m181218_204719_handle_add_column_user
 */
class m181218_204719_handle_add_column_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('handle', 'user_id', 'INTEGER NOT NULL AFTER id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('handle', 'user_id');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181218_204719_handle_add_column_user cannot be reverted.\n";

        return false;
    }
    */
}
