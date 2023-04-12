<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m181216_235130_create_handle
 */
class m181216_235130_create_handle extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('handle', [
         'id' => Schema::TYPE_PK . ' AUTO_INCREMENT',
         'handle' => Schema::TYPE_STRING . ' NOT NULL',
         'name' => Schema::TYPE_STRING,
         'verified' => $this->boolean(),
         'user_since' => $this->date(),
         'profile_image' => $this->string()
     ]);

     return true;

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropTable('handle');
      return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181216_235130_create_handle cannot be reverted.\n";

        return false;
    }
    */
}
