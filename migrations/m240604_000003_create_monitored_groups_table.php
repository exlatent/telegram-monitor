<?php

use yii\db\Migration;

class m240604_000003_create_monitored_groups_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%monitored_groups}}', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'telegram_username' => $this->string(255)->null(),
            'telegram_id' => $this->bigInteger()->null(),
            'name' => $this->string(255)->notNull(),
            'last_message_id' => $this->integer()->null()->comment('Last processed message ID'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-monitored_groups-project_id', '{{%monitored_groups}}', 'project_id');
        $this->createIndex('idx-monitored_groups-telegram_id', '{{%monitored_groups}}', 'telegram_id');
        $this->createIndex('idx-monitored_groups-telegram_username', '{{%monitored_groups}}', 'telegram_username');

        $this->addForeignKey(
            'fk-monitored_groups-project_id',
            '{{%monitored_groups}}',
            'project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-monitored_groups-project_id', '{{%monitored_groups}}');
        $this->dropTable('{{%monitored_groups}}');
    }
}
