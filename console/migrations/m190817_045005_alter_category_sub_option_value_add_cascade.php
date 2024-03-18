<?php

use yii\db\Migration;

/**
 * Class m190817_045005_alter_category_sub_option_value_add_cascade
 */
class m190817_045005_alter_category_sub_option_value_add_cascade extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->dropForeignKey('fk-category_sub_option_value-category_option_value_id', 'category_sub_option_value');
      $this->addForeignKey('fk-category_sub_option_value-category_option_value_id', 'category_sub_option_value', 'category_option_value_id', 'category_option_value', 'id', 'CASCADE');
      return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropForeignKey('fk-category_sub_option_value-category_option_value_id', 'category_sub_option_value');
      $this->addForeignKey('fk-category_sub_option_value-category_option_value_id', 'category_sub_option_value', 'category_option_value_id', 'category_option_value', 'id');
      return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190817_045005_alter_category_sub_option_value_add_cascade cannot be reverted.\n";

        return false;
    }
    */
}
