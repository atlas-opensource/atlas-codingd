<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m181216_235303_create_tweet
 */
class m181216_235303_create_tweet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      # Create table
      $this->createTable('tweet',
      [
          'id' => $this->primaryKey(),
          'handle_id' => $this->integer()->notNull(),
          'date' => $this->dateTime(),
          'tweet_id' => $this->string(),
          'tweet_text' => $this->string(),
          'app' => $this->string(),
          'followers' => $this->integer(),
          'follows' => $this->integer(),
          'retweets' => $this->integer(),
          'favorites' => $this->integer(),
          'location' => $this->string()
      ]);

      # Create index
      $this->createIndex(
          'idx-tweet-handle_id',
          'tweet',
          'handle_id'
      );

      // add foreign key for table `post`
      $this->addForeignKey(
          'fk-tweet-handle_id',
          'tweet',
          'handle_id',
          'tweet',
          'id',
          'CASCADE'
      );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropTable('tweet');
      return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181216_235303_create_tweet cannot be reverted.\n";

        return false;
    }
    */
}
