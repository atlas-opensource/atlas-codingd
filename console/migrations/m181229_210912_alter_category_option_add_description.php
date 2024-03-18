<?php

use yii\db\Migration;

/**
 * Class m181229_210912_alter_category_option_add_description
 */
class m181229_210912_alter_category_option_add_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Create and return
        $this->addColumn('category_option', 'description', 'VARCHAR(500) AFTER name');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop and return
        $this->dropColumn('category_option', 'description');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181229_210912_alter_category_option_add_description cannot be reverted.\n";

        return false;
    }
    */
}
