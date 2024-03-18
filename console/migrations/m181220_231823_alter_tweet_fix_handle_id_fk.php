<?php

use yii\db\Migration;

/**
 * Class m181220_231823_alter_tweet_fix_handle_id_fk
 */
class m181220_231823_alter_tweet_fix_handle_id_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Remove old foreign key (broken)
        $this->dropForeignKey('fk-tweet-handle_id', "tweet");

        # Add foreign key for table `post`
        $this->addForeignKey(
            'fk-tweet-handle_id',
            'tweet',
            'handle_id',
            'handle',
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
        # Remove old foreign key (broken)
        $this->dropForeignKey('fk-tweet-handle_id', "tweet");

        # Add foreign key for table `post`
        $this->addForeignKey(
            'fk-tweet-handle_id',
            'tweet',
            'handle_id',
            'tweet',
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
        echo "m181220_231823_alter_tweet_fix_handle_id_fk cannot be reverted.\n";

        return false;
    }
    */
}
