<?php

use yii\db\Migration;

/**
 * Class m190224_155310_add_foreign_key_to_category
 */
class m190224_155310_add_foreign_key_to_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190224_155310_add_foreign_key_to_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190224_155310_add_foreign_key_to_category cannot be reverted.\n";

        return false;
    }
    */
}
