<?php

use yii\db\Migration;

/**
 * Class m191012_154533_2019_10_12_handle_before_after_ids
 */
class m191012_154533_2019_10_12_handle_before_after_ids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('handle', 'before_id', 'BIGINT(20)');
        $this->addColumn('handle', 'after_id', 'BIGINT(20)');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('handle', 'before_id');
        $this->dropColumn('handle', 'after_id');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191012_154533_2019_10_12_handle_before_after_ids cannot be reverted.\n";

        return false;
    }
    */
}
