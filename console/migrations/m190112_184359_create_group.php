<?php

use yii\db\Migration;

/**
 * Class m190112_184359_create_group
 */
class m190112_184359_create_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('group',
        [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
        ]);

        $this->createTable('group_member',
        [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-group_member-user_id',
            'group_member',
            'user_id'
        );

        $this->createIndex(
            'idx-group_member-group_id',
            'group_member',
            'group_id'
        );

        // add foreign key to the user table
        $this->addForeignKey(
            'fk-group_member-user_id',
            'group_member',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // add foreign key to the group table
        $this->addForeignKey(
            'fk-group_member-group_id',
            'group_member',
            'group_id',
            'group',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop indexes, keys, and tables
        $this->dropForeignKey('fk-group_member-user_id', 'group_member');
        $this->dropForeignKey('fk-group_member-group_id', 'group_member');
        $this->dropIndex('idx-group_member-user_id', 'group_member');
        $this->dropIndex('idx-group_member-group_id', 'group_member');
        $this->dropTable('group_member');
        $this->dropTable('group');

        # Return
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190112_184359_create_group cannot be reverted.\n";

        return false;
    }
    */
}
