<?php

use yii\db\Migration;

/**
 * Class m190119_183416_alter_category_sub_option_value_key_to_category_option_value
 */
class m190119_183416_alter_category_sub_option_value_key_to_category_option_value extends Migration
{
    /*
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('category_sub_option_value', 'category_option_value_id', 'INTEGER NOT NULL');
        $this->createIndex('idx-category_sub_option_value-category_option_value_id', 'category_sub_option_value', 'category_option_value_id');
        $this->addForeignKey('fk-category_sub_option_value-category_option_value_id', 'category_sub_option_value', 'category_option_value_id', 'category_option_value', 'id');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-category_sub_option_value-category_option_value_id', 'category_sub_option_value');
        $this->dropIndex('idx-category_sub_option_value-category_option_value_id', 'category_sub_option_value');
        $this->dropColumn('category_sub_option_value', 'category_option_value_id');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190119_183416_alter_category_sub_option_value_key_to_category_option_value cannot be reverted.\n";

        return false;
    }
    */
}
