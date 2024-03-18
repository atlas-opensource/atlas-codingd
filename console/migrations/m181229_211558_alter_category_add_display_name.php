<?php

use yii\db\Migration;

/**
 * Class m181229_211558_alter_category_add_display_name
 */
class m181229_211558_alter_category_add_display_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Add and return
        $this->addColumn('category', 'display_name', 'VARCHAR(255)');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop column and return
        $this->dropColumn('category', 'display_name');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181229_211558_alter_category_add_display_name cannot be reverted.\n";

        return false;
    }
    */
}
