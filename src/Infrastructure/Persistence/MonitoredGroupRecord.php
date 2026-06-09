<?php

namespace app\Infrastructure\Persistence;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @property int $id
 * @property int $project_id
 * @property string|null $telegram_username
 * @property int|null $telegram_id
 * @property string $name
 * @property int|null $last_message_id
 * @property int $created_at
 * @property int $updated_at
 */
class MonitoredGroupRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%monitored_groups}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['project_id', 'name'], 'required'],
            [['project_id', 'telegram_id', 'last_message_id'], 'integer'],
            [['telegram_username', 'name'], 'string', 'max' => 255],
        ];
    }

    public function getProject()
    {
        return $this->hasOne(ProjectRecord::class, ['id' => 'project_id']);
    }

    public function getMessages()
    {
        return $this->hasMany(MessageRecord::class, ['group_id' => 'id']);
    }
}
