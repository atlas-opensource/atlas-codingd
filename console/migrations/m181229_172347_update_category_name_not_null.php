<?php

use yii\db\Migration;

/**
 * Class m181229_172347_update_category_name_not_null
 */
class m181229_172347_update_category_name_not_null extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Make name not null
        $this->alterColumn('category', 'name', $this->string()->notNull());
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Revert
        $this->alterColumn('category', 'name', $this->string());
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181229_172347_update_category_name_not_null cannot be reverted.\n";

        return false;
    }
    */
}
