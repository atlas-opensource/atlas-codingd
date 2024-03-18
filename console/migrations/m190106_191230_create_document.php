<?php

use yii\db\Migration;

/**
 * Class m190106_191230_create_document
 */
class m190106_191230_create_document extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('document',
        [
            'id' => $this->primaryKey(),
        ]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('document');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190106_191230_create_document cannot be reverted.\n";

        return false;
    }
    */
}
