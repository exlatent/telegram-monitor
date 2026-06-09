<?php

use yii\db\Migration;

class m240604_000006_create_matches_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%matches}}', [
            'id' => $this->primaryKey(),
            'message_id' => $this->integer()->notNull(),
            'keyword_id' => $this->integer()->notNull(),
            'project_id' => $this->integer()->notNull(),
            'is_sent' => $this->boolean()->notNull()->defaultValue(false),
            'sent_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-matches-message_id', '{{%matches}}', 'message_id');
        $this->createIndex('idx-matches-keyword_id', '{{%matches}}', 'keyword_id');
        $this->createIndex('idx-matches-project_id', '{{%matches}}', 'project_id');
        $this->createIndex('idx-matches-is_sent', '{{%matches}}', 'is_sent');

        // Уникальность по комбинации message_id + keyword_id + project_id
        $this->createIndex(
            'idx-matches-unique',
            '{{%matches}}',
            ['message_id', 'keyword_id', 'project_id'],
            true
        );

        $this->addForeignKey(
            'fk-matches-message_id',
            '{{%matches}}',
            'message_id',
            '{{%messages}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-matches-keyword_id',
            '{{%matches}}',
            'keyword_id',
            '{{%keywords}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-matches-project_id',
            '{{%matches}}',
            'project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-matches-message_id', '{{%matches}}');
        $this->dropForeignKey('fk-matches-keyword_id', '{{%matches}}');
        $this->dropForeignKey('fk-matches-project_id', '{{%matches}}');
        $this->dropTable('{{%matches}}');
    }
}
