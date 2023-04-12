<?php

use yii\db\Migration;

/**
 * Class m191012_143931_2019_10_12_rate_limits
 */
class m191012_143931_2019_10_12_rate_limits extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('twitter_api', 'x_rate_limit_limit', 'INTEGER');
        $this->addColumn('twitter_api', 'x_rate_limit_remaining', 'INTEGER');
        $this->addColumn('twitter_api', 'x_rate_limit_reset', 'INTEGER');
        $this->addColumn('twitter_api', 'endpoint_handle', 'BLOB');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('twitter_api', 'x_rate_limit_limit');
        $this->dropColumn('twitter_api', 'x_rate_limit_remaining');
        $this->dropColumn('twitter_api', 'x_rate_limit_reset');
        $this->dropColumn('twitter_api', 'endpoint_handle');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191012_143931_2019_10_12_rate_limits cannot be reverted.\n";

        return false;
    }
    */
}
