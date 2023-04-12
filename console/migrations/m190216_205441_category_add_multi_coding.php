<?php

use yii\db\Migration;

/**
 * Class m190216_205441_category_add_multi_coding
 */
class m190216_205441_category_add_multi_coding extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Define return
        $returnVal = true;

        # Try to add column
        $this->addColumn('category', 'allow_multiple', $this->boolean()->defaultValue(false)->notNull());

        # Set engagement to true
        $this->update('category', ['allow_multiple' => true], ['display_name' => 'Engagement']);

        # Return
        return $returnVal;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Define return
        $returnVal = true;

        # Try to drop column
        $this->dropColumn('category', 'allow_multiple');

        # Return
        return $returnVal;
    }
}
