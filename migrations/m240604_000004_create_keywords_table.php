<?php

use yii\db\Migration;

class m240604_000004_create_keywords_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%keywords}}', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'word' => $this->string(255)->notNull(),
            'normalized_forms' => $this->text()->null()->comment('JSON array of normalized forms'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-keywords-project_id', '{{%keywords}}', 'project_id');

        $this->addForeignKey(
            'fk-keywords-project_id',
            '{{%keywords}}',
            'project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-keywords-project_id', '{{%keywords}}');
        $this->dropTable('{{%keywords}}');
    }
}
