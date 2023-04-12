<?php

use yii\db\Migration;

/**
 * Class m181218_230038_handle_add_column_category
 */
class m181218_230038_handle_add_column_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('handle', 'label', 'VARCHAR(50)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('handle', 'label');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181218_230038_handle_add_column_category cannot be reverted.\n";

        return false;
    }
    */
}
