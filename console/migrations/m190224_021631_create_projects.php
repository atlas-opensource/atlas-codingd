<?php

use yii\db\Migration;

/**
 * Class m190224_021631_create_projects
 */
class m190224_021631_create_projects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        # Create project table
        $this->createTable('project',
        [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(),
        ]);

        # Create index
        $this->createIndex
        (
            'idx-project-user_id',
            'project',
            'user_id'
        );

        # Add foreign key from project(user_id) to user(id)
        $this->addForeignKey(
            'fk-project-user_id',
            'project',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        # Create project -> handle pivot
        $this->createTable("xref_project_handle",
        [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer(),
            'handle_id' => $this->integer(),
        ]);

        # Add index on xref_project_handle project_id
        $this->createIndex("idx-xref_project_handle-project_id", "xref_project_handle", "project_id");

        # Add index on xref_project_handle handle_id
        $this->createIndex("idx-xref_project_handle-handle_id", "xref_project_handle", "handle_id");

        # Add foreign key from xref_project_handle(project_id) to project(id)
        $this->addForeignKey('fk-xref_project_handle-project_id', 'xref_project_handle', 'project_id', 'project', 'id', 'CASCADE');

        # Add foreign key from xref_project_handle(handle_id) to handle(id)
        $this->addForeignKey('fk-xref_project_handle-handle_id', 'xref_project_handle', 'handle_id', 'handle', 'id', 'CASCADE');

        # Create project -> user pivot
        $this->createTable("xref_project_user",
        [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer(),
            'user_id' => $this->integer(),
        ]);

        # Add index on xref_project_user project_id
        $this->createIndex("idx-xref_project_user-project_id", "xref_project_user", "project_id");

        # Add index on xref_project_user user_id
        $this->createIndex("idx-xref_project_user-user_id", "xref_project_user", "user_id");

        # Add foreign key from xref_project_user(project_id) to project(id)
        $this->addForeignKey('fk-xref_project_user-project_id', 'xref_project_user', 'project_id', 'project', 'id', 'CASCADE');

        # Add foreign key from xref_project_user(user_id) to user(id)
        $this->addForeignKey('fk-xref_project_user-user_id', 'xref_project_user', 'user_id', 'user', 'id', 'CASCADE');

        # Return
        return true;

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        # Drop foreign key from xref_project_handle(project_id) to project(id)
        $this->dropForeignKey('fk-xref_project_handle-project_id', 'xref_project_handle');

        # Drop foreign key from xref_project_handle(handle_id) to handle(id)
        $this->dropForeignKey('fk-xref_project_handle-handle_id', 'xref_project_handle');

        # Drop index on xref_project_handle project_id
        $this->dropIndex("idx-xref_project_handle-project_id", "xref_project_handle");

        # Drop index on xref_project_handle handle_id
        $this->dropIndex("idx-xref_project_handle-handle_id", "xref_project_handle");

        # Drop foreign key from xref_project_user(project_id) to project(id)
        $this->dropForeignKey('fk-xref_project_user-project_id', 'xref_project_user');

        # Drop foreign key from xref_project_user(user_id) to user(id)
        $this->dropForeignKey('fk-xref_project_user-user_id', 'xref_project_user');

        # Drop index on xref_project_user project_id
        $this->dropIndex("idx-xref_project_user-project_id", "xref_project_user");

        # Drop index on xref_project_user user_id
        $this->dropIndex("idx-xref_project_user-user_id", "xref_project_user");

        # Drop project handle pivot
        $this->dropTable('xref_project_handle');

        # Drop project user pivot
        $this->dropTable('xref_project_user');

        # Drop original project table
        $this->dropForeignKey('fk-project-user_id', 'project');
        $this->dropIndex('idx-project-user_id', 'project');
        $this->dropTable('project');

        # Return
        return true;
    }
}
