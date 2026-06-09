<?php

use yii\db\Migration;

class m240604_000005_create_messages_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%messages}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer()->notNull(),
            'telegram_message_id' => $this->integer()->notNull(),
            'text' => $this->text()->null(),
            'message_date' => $this->integer()->notNull(),
            'link' => $this->string(500)->null(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-messages-group_id', '{{%messages}}', 'group_id');
        $this->createIndex('idx-messages-message_date', '{{%messages}}', 'message_date');

        // Уникальность по комбинации group_id + telegram_message_id
        $this->createIndex(
            'idx-messages-unique',
            '{{%messages}}',
            ['group_id', 'telegram_message_id'],
            true
        );

        $this->addForeignKey(
            'fk-messages-group_id',
            '{{%messages}}',
            'group_id',
            '{{%monitored_groups}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-messages-group_id', '{{%messages}}');
        $this->dropTable('{{%messages}}');
    }
}
