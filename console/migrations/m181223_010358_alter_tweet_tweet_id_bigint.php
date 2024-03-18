<?php

use yii\db\Migration;

/**
 * Class m181223_010358_alter_tweet_tweet_id_bigint
 */
class m181223_010358_alter_tweet_tweet_id_bigint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('tweet', 'tweet_id', 'BIGINT');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('tweet', 'tweet_id', 'VARCHAR(255)');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181223_010358_alter_tweet_tweet_id_bigint cannot be reverted.\n";

        return false;
    }
    */
}
