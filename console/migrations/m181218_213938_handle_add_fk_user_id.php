<?php

use yii\db\Migration;

/**
 * Class m181218_213938_handle_add_fk_user_id
 */
class m181218_213938_handle_add_fk_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /*
        # Create index
        $this->createIndex(
            'idx-handle-user_id',
            'handle',
            'user_id'
        );
        */

        # Add foreign key for table `post`
        $this->addForeignKey(
            'fk-handle-user_id',
            'handle',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop index and foreign key
        $this->dropForeignKey('fk-handle-user_id', 'handle');
        $this->dropIndex('idx-handle-user_id', 'handle');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181218_213938_handle_add_fk_user_id cannot be reverted.\n";

        return false;
    }
    */
}
