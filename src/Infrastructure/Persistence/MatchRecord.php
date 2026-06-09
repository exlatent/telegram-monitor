<?php

namespace app\Infrastructure\Persistence;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $message_id
 * @property int $keyword_id
 * @property int $project_id
 * @property bool $is_sent
 * @property int|null $sent_at
 * @property int $created_at
 */
class MatchRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%matches}}';
    }

    public function rules(): array
    {
        return [
            [['message_id', 'keyword_id', 'project_id'], 'required'],
            [['message_id', 'keyword_id', 'project_id', 'sent_at', 'created_at'], 'integer'],
            [['is_sent'], 'boolean'],
        ];
    }

    public function getMessage()
    {
        return $this->hasOne(MessageRecord::class, ['id' => 'message_id']);
    }

    public function getKeyword()
    {
        return $this->hasOne(KeywordRecord::class, ['id' => 'keyword_id']);
    }

    public function getProject()
    {
        return $this->hasOne(ProjectRecord::class, ['id' => 'project_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = time();
            }
            return true;
        }
        return false;
    }
}
