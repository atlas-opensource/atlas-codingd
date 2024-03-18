<?php

use yii\db\Migration;

/**
 * Class m190210_045420_set_tweet_retweet_len
 */
class m190210_045420_set_tweet_retweet_len extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('tweet', 'retweet_text', $this->string(800));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('tweet', 'retweet_text', $this->string());
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190210_045420_set_tweet_retweet_len cannot be reverted.\n";

        return false;
    }
    */
}
