<?php

use yii\db\Migration;

/**
 * Class m190117_195112_add_column_category_sub_option_code
 */
class m190117_195112_add_column_category_sub_option_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category_sub_option', 'code', 'varchar(255) NOT NULL AFTER category_option_id');
        $this->dropColumn('category_sub_option', 'display_name');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('category_sub_option', 'code');
        $this->addColumn('category_sub_option', 'display_name', 'varchar(255) AFTER name');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190117_195112_add_column_category_sub_option_code cannot be reverted.\n";

        return false;
    }
    */
}
