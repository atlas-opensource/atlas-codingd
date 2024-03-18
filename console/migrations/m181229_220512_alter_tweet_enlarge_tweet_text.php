<?php

use yii\db\Migration;

/**
 * Class m181229_220512_alter_tweet_enlarge_tweet_text
 */
class m181229_220512_alter_tweet_enlarge_tweet_text extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('tweet', 'tweet_text', 'VARCHAR(800)');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('tweet', 'tweet_text', 'VARCHAR(255)');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181229_220512_alter_tweet_enlarge_tweet_text cannot be reverted.\n";

        return false;
    }
    */
}
