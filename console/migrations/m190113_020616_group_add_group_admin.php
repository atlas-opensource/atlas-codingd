<?php

use yii\db\Migration;

/**
 * Class m190113_020616_group_add_group_admin
 */
class m190113_020616_group_add_group_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('group', 'admin_id', 'integer NOT NULL AFTER id');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('group', 'admin_id');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190113_020616_group_add_group_admin cannot be reverted.\n";

        return false;
    }
    */
}
