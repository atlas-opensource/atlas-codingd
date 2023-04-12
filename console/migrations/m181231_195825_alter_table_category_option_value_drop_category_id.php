<?php

use yii\db\Migration;

/**
 * Class m181231_195825_alter_table_category_option_value_drop_category_id
 */
class m181231_195825_alter_table_category_option_value_drop_category_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Drop keys, indees and column
        $this->dropForeignKey('fk-category_option_value-category_id', 'category_option_value');
        $this->dropIndex('idx-category_option_value-category_id', 'category_option_value');
        $this->dropColumn('category_option_value', 'category_id');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Add Column
        $this->addColumn('category_option_value', 'category_id', 'INTEGER NOT NULL AFTER tweet_id');

        # Create category index
        $this->createIndex(
            'idx-category_option_value-category_id',
            'category_option_value',
            'category_id'
        );

        # Add foreign key to category
        $this->addForeignKey(
            'fk-category_option_value-category_id',
            'category_option_value',
            'category_id',
            'category',
            'id',
            'CASCADE'
        );


        # Return
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181231_195825_alter_table_category_option_value_drop_category_id cannot be reverted.\n";

        return false;
    }
    */
}
