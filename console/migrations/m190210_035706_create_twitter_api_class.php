<?php

use yii\db\Migration;

/**
 * Class m190210_035706_create_twitter_api_class
 */
class m190210_035706_create_twitter_api_class extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('twitter_api',
        [
            'id' => $this->primaryKey(),
            'handle' => $this->string()->notNull(),
            'consumer_key' => $this->string()->notNull(),
            'consumer_secret' => $this->string()->notNull(),
            'oauth_token' => $this->string()->notNull(),
            'token_secret' => $this->string()->notNull(),
            'persist_data' => $this->text(),
            'updated_at' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime(),
            'active' => $this->boolean()->defaultValue(false),
        ]);

        # Add last updated stamp column to handle
        $this->addColumn('handle', 'last_update', $this->dateTime());

        # Add retweet_text column to tweet table
        $this->addColumn('tweet', 'retweet_text', $this->text());

        # Return
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop new twitter api table
        $this->dropTable('twitter_api');

        # Drop new handle updated column
        $this->dropColumn('handle', 'last_update');

        # Drop new tweet column
        $this->dropColumn('tweet', 'retweet_text');

        return true;
    }
}
