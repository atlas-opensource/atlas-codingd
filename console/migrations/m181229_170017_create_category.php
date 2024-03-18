<?php

use yii\db\Migration;

/**
 * Class m181229_170017_create_category
 */
class m181229_170017_create_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Create and return
        $this->createTable('category',
        [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(255),
        ]);

        # Create index
        $this->createIndex(
            'idx-category-user_id',
            'category',
            'user_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-category-user_id',
            'category',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Remove and return
        $this->dropForeignKey('fk-category-user_id', 'category');
        $this->dropIndex('idx-category-user_id', 'category');
        $this->dropTable('category');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181229_170017_create_category cannot be reverted.\n";

        return false;
    }
    */
}
