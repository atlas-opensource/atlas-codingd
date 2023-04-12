<?php

use yii\db\Migration;

/**
 * Class m181229_171757_create_category_option
 */
class m181229_171757_create_category_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Create table
        $this->createTable('category_option',
        [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
        ]);

        # Create index
        $this->createIndex(
            'idx-category_option-cateogry_id',
            'category_option',
            'category_id'
        );

        # Create foreign key
        $this->addForeignKey(
            'fk-category_option-category_id',
            'category_option',
            'category_id',
            'category',
            'id',
            'CASCADE'
        );

        # Return
        return true;

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop and return
        $this->dropForeignKey('fk-category_option-category_id', 'category_option');
        $this->dropIndex('idx-category_option-cateogry_id', 'category_option');
        $this->dropTable('category_option');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181229_171757_create_category_option cannot be reverted.\n";

        return false;
    }
    */
}
