<?php

namespace app\Infrastructure\Persistence;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $digest_interval
 * @property bool $is_active
 * @property int|null $last_digest_sent_at
 * @property int $created_at
 * @property int $updated_at
 */
class ProjectRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%projects}}';
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
            [['user_id', 'name', 'digest_interval'], 'required'],
            [['user_id', 'digest_interval', 'last_digest_sent_at'], 'integer'],
            [['is_active'], 'boolean'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(UserRecord::class, ['id' => 'user_id']);
    }

    public function getMonitoredGroups()
    {
        return $this->hasMany(MonitoredGroupRecord::class, ['project_id' => 'id']);
    }

    public function getKeywords()
    {
        return $this->hasMany(KeywordRecord::class, ['project_id' => 'id']);
    }
}
