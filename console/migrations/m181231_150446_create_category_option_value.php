<?php

use yii\db\Migration;

/**
 * Class m181231_150446_create_category_option_value
 */
class m181231_150446_create_category_option_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category_option_value',
        [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'tweet_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'category_option_id' => $this->integer()->notNull(),
        ]);

        # Create user index
        $this->createIndex(
            'idx-category_option_value-user_id',
            'category_option_value',
            'user_id'
        );

        # Add foreign key to user
        $this->addForeignKey(
            'fk-category_option_value-user_id',
            'category_option_value',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        # Create tweet index
        $this->createIndex(
            'idx-category_option_value-tweet_id',
            'category_option_value',
            'tweet_id'
        );

        # Add foreign key to tweet
        $this->addForeignKey(
            'fk-category_option_value-tweet_id',
            'category_option_value',
            'tweet_id',
            'tweet',
            'id',
            'CASCADE'
        );

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

        # Create category option index
        $this->createIndex(
            'idx-category_option_value-category_option_id',
            'category_option_value',
            'category_option_id'
        );

        # Add foreign key to category option
        $this->addForeignKey(
            'fk-category_option_value-category_option_id',
            'category_option_value',
            'category_option_id',
            'category_option',
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
        # Drop user index and key
        $this->dropForeignKey('fk-category_option_value-user_id', 'category_option_value');
        $this->dropIndex('idx-category_option_value-user_id', 'category_option_value');

        # Drop tweet index and key
        $this->dropForeignKey('fk-category_option_value-tweet_id', 'category_option_value');
        $this->dropIndex('idx-category_option_value-tweet_id', 'category_option_value');

        # Drop category index and key
        $this->dropForeignKey('fk-category_option_value-category_id', 'category_option_value');
        $this->dropIndex('idx-category_option_value-category_id', 'category_option_value');

        # Drop category option index and key
        $this->dropForeignKey('fk-category_option_value-category_option_id', 'category_option_value');
        $this->dropIndex('idx-category_option_value-category_option_id', 'category_option_value');

        # Drop table
        $this->dropTable('category_option_value');

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
        echo "m181231_150446_create_category_option_value cannot be reverted.\n";

        return false;
    }
    */
}
