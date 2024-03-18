<?php

use yii\db\Migration;

/**
 * Class m190117_175417_create_category_sub_option
 */
class m190117_175417_create_category_sub_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category_sub_option',
        [
            'id' => $this->primaryKey(),
            'category_option_id' => $this->integer(),
            'name' => $this->string(),
            'display_name' => $this->string(),
            'description' => $this->text(),
        ]);

        # Add index
        $this->createIndex(
            'idx-category_sub_option-category_option_id',
            'category_sub_option',
            'category_option_id'
        );

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
        # Drop and return
        $this->dropForeignKey('fk-category_sub_option-category_option_id', 'category_sub_option');
        $this->dropIndex('idx-category_sub_option-category_option_id', 'category_sub_option');
        $this->dropTable('category_sub_option');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190117_175417_create_category_sub_option cannot be reverted.\n";

        return false;
    }
    */
}
