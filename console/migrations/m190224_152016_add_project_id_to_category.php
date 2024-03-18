<?php

use yii\db\Migration;

/**
 * Class m190224_152016_add_project_id_to_category
 */
class m190224_152016_add_project_id_to_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        # Add project_id field to category table
        $this->addColumn("category", "project_id", "INTEGER NOT NULL AFTER user_id");

        # Add index and foreign key to above field
        $this->createIndex("idx-category-project_id", "category", 'project_id');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop fk, idx, and column
        $this->dropIndex("idx-category-project_id", "category");
        $this->dropColumn("category", "project_id");

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190224_152016_add_project_id_to_category cannot be reverted.\n";

        return false;
    }
    */
}
