<?php

namespace app\Infrastructure\Persistence;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $group_id
 * @property int $telegram_message_id
 * @property string|null $text
 * @property int $message_date
 * @property string|null $link
 * @property int $created_at
 */
class MessageRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%messages}}';
    }

    public function rules(): array
    {
        return [
            [['group_id', 'telegram_message_id', 'message_date'], 'required'],
            [['group_id', 'telegram_message_id', 'message_date', 'created_at'], 'integer'],
            [['text'], 'string'],
            [['link'], 'string', 'max' => 500],
        ];
    }

    public function getGroup()
    {
        return $this->hasOne(MonitoredGroupRecord::class, ['id' => 'group_id']);
    }

    public function getMatches()
    {
        return $this->hasMany(MatchRecord::class, ['message_id' => 'id']);
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
