<?php

use yii\db\Migration;

class m240604_000008_add_admin_fields_to_users extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'is_admin', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%users}}', 'password_hash', $this->string()->null());
        
        $this->createIndex('idx-users-is_admin', '{{%users}}', 'is_admin');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'password_hash');
        $this->dropColumn('{{%users}}', 'is_admin');
    }
}
