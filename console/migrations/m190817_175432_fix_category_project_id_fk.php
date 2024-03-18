<?php

use yii\db\Migration;

/**
 * Class m190817_175432_fix_category_project_id_fk
 */
class m190817_175432_fix_category_project_id_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Drop existing keys
        //$this->dropForeignKey('category_ibfk_1', 'category');
        //$this->dropForeignKey('category_ibfk_2', 'category');

        # Add new relationship 
        $this->addForeignKey('fk-category-project_id-project-id', 'category', 'project_id', 'project', 'id', 'CASCADE');

        # Return success
        return true;  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop new relationship
        $this->dropForeignKey('fk-category-project_id-project-id', 'category');

        # Add back old relationships
        //$this->addForeignKey('category_ibfk_1', 'category', 'project_id', 'project', 'id');
        //$this->addForeignKey('category_ibfk_2', 'category', 'project_id', 'project', 'id');

        # Return success
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190817_175432_fix_category_project_id_fk cannot be reverted.\n";

        return false;
    }
    */
}
