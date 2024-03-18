<?php

use yii\db\Migration;

/**
 * Class m181231_012712_alter_category_option_add_number_value
 */
class m181231_012712_alter_category_option_add_number_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category_option', 'code', 'INTEGER NOT NULL AFTER category_id');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category_option', 'code');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181231_012712_alter_category_option_add_number_value cannot be reverted.\n";

        return false;
    }
    */
}
