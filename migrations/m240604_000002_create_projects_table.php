<?php

use yii\db\Migration;

class m240604_000002_create_projects_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%projects}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'digest_interval' => $this->integer()->notNull()->comment('Interval in minutes'),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'last_digest_sent_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-projects-user_id', '{{%projects}}', 'user_id');
        $this->createIndex('idx-projects-is_active', '{{%projects}}', 'is_active');

        $this->addForeignKey(
            'fk-projects-user_id',
            '{{%projects}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-projects-user_id', '{{%projects}}');
        $this->dropTable('{{%projects}}');
    }
}
