<?php

use yii\db\Migration;
use yii\db\Schema;


/**
 * Class m181229_164840_create_classifier
 */
class m181229_164840_create_classifier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Create and return
        $this->createTable('classifier',
        [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(),
            'target_type' => "ENUM('text', 'image', 'audio', 'blob', 'tcpdump')",
            'end_point' => $this->string(),
        ]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop and return
        $this->dropTable('classifier');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181229_164840_create_classifier cannot be reverted.\n";

        return false;
    }
    */
}
