<?php

use yii\db\Migration;

/**
 * Class m190117_182023_create_category_sub_option_value
 */
class m190117_182023_create_category_sub_option_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Create table
        $this->createTable('category_sub_option_value',
        [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'tweet_id' => $this->integer()->notNull(),
            'category_sub_option_id' => $this->integer()->notNull(),
        ]);

        # Create user index
        $this->createIndex(
            'idx-category_sub_option_value-user_id',
            'category_sub_option_value',
            'user_id'
        );

        # Add foreign key to user
        $this->addForeignKey(
            'fk-category_sub_option_value-user_id',
            'category_sub_option_value',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        # Create tweet index
        $this->createIndex(
            'idx-category_sub_option_value-tweet_id',
            'category_sub_option_value',
            'tweet_id'
        );

        # Add foreign key to tweet
        $this->addForeignKey(
            'fk-category_sub_option_value-tweet_id',
            'category_sub_option_value',
            'tweet_id',
            'tweet',
            'id',
            'CASCADE'
        );

        # Create category option index
        $this->createIndex(
            'idx-category_sub_option_value-category_sub_option_id',
            'category_sub_option_value',
            'category_sub_option_id'
        );

        # Add foreign key to category
        $this->addForeignKey(
            'fk-category_sub_option_value-category_sub_option_id',
            'category_sub_option_value',
            'category_sub_option_id',
            'category_sub_option',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop foreign key to user
        $this->dropForeignKey(
            'fk-category_sub_option_value-user_id',
            'category_sub_option_value'
        );

        # Drop foreign key to tweet
        $this->dropForeignKey(
            'fk-category_sub_option_value-tweet_id',
            'category_sub_option_value'
        );

        # Drop foreign key to category sub option
        $this->dropForeignKey(
            'fk-category_sub_option_value-category_sub_option_id',
            'category_sub_option_value'
        );

        # Drop user index
        $this->dropIndex(
            'idx-category_sub_option_value-user_id',
            'category_sub_option_value'
        );

        # Drop category option index
        $this->dropIndex(
            'idx-category_sub_option_value-category_sub_option_id',
            'category_sub_option_value'
        );

        # Drop tweet index
        $this->dropIndex(
            'idx-category_sub_option_value-tweet_id',
            'category_sub_option_value'
        );

        # Drop table
        $this->dropTable('category_sub_option_value');

        return true;
    }
}
