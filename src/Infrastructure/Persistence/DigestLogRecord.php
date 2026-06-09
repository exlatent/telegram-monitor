<?php

namespace app\Infrastructure\Persistence;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $project_id
 * @property int $user_id
 * @property int $matches_count
 * @property string|null $message_text
 * @property int $sent_at
 * @property int $created_at
 */
class DigestLogRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%digest_logs}}';
    }

    public function rules(): array
    {
        return [
            [['project_id', 'user_id', 'sent_at'], 'required'],
            [['project_id', 'user_id', 'matches_count', 'sent_at', 'created_at'], 'integer'],
            [['message_text'], 'string'],
        ];
    }

    public function getProject()
    {
        return $this->hasOne(ProjectRecord::class, ['id' => 'project_id']);
    }

    public function getUser()
    {
        return $this->hasOne(UserRecord::class, ['id' => 'user_id']);
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
