<?php

use yii\db\Migration;

class m240604_000007_create_digest_logs_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%digest_logs}}', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'matches_count' => $this->integer()->notNull()->defaultValue(0),
            'message_text' => $this->text()->null(),
            'sent_at' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-digest_logs-project_id', '{{%digest_logs}}', 'project_id');
        $this->createIndex('idx-digest_logs-user_id', '{{%digest_logs}}', 'user_id');
        $this->createIndex('idx-digest_logs-sent_at', '{{%digest_logs}}', 'sent_at');

        $this->addForeignKey(
            'fk-digest_logs-project_id',
            '{{%digest_logs}}',
            'project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-digest_logs-user_id',
            '{{%digest_logs}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-digest_logs-project_id', '{{%digest_logs}}');
        $this->dropForeignKey('fk-digest_logs-user_id', '{{%digest_logs}}');
        $this->dropTable('{{%digest_logs}}');
    }
}
