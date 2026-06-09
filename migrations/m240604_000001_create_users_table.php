<?php

use yii\db\Migration;

class m240604_000001_create_users_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'telegram_id' => $this->bigInteger()->notNull()->unique(),
            'username' => $this->string(255)->null(),
            'first_name' => $this->string(255)->null(),
            'last_name' => $this->string(255)->null(),
            'status' => $this->string(20)->notNull()->defaultValue('active'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-users-telegram_id', '{{%users}}', 'telegram_id');
        $this->createIndex('idx-users-status', '{{%users}}', 'status');
    }

    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
