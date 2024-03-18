<?php

use yii\db\Migration;

/**
 * Class m190117_190658_add_category_sub_option_not_null_constraints
 */
class m190117_190658_add_category_sub_option_not_null_constraints extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Drop key on category_option
        $this->dropForeignKey('fk-category_sub_option-category_option_id', 'category_sub_option');

        # Add constraints
        $this->alterColumn('category_sub_option', 'category_option_id', $this->integer()->notNull());
        $this->alterColumn('category_sub_option', 'name', $this->string()->notNull());

        # Add foreign key to the cateogry option table
        $this->addForeignKey(
            'fk-category_sub_option-category_option_id',
            'category_sub_option',
            'category_option_id',
            'category_option',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        # Drop key on category_option
        $this->dropForeignKey('fk-category_sub_option-category_option_id', 'category_sub_option');

        # Drop not null constraints
        $this->alterColumn('category_sub_option', 'category_option_id', $this->integer());
        $this->alterColumn('category_sub_option', 'name', $this->string());

        # Add foreign key to the cateogry option table
        $this->addForeignKey(
            'fk-category_sub_option-category_option_id',
            'category_sub_option',
            'category_option_id',
            'category_option',
            'id',
            'CASCADE'
        );

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190117_190658_add_category_sub_option_not_null_constraints cannot be reverted.\n";

        return false;
    }
    */
}
