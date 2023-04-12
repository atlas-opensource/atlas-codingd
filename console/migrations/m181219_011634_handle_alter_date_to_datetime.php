<?php

use yii\db\Migration;

/**
 * Class m181219_011634_handle_alter_date_to_datetime
 */
class m181219_011634_handle_alter_date_to_datetime extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('handle', 'user_since', 'DATETIME');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('handle', 'user_since', 'DATE');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181219_011634_handle_alter_date_to_datetime cannot be reverted.\n";

        return false;
    }
    */
}
