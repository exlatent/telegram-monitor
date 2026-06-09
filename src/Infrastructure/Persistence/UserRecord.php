<?php

namespace app\Infrastructure\Persistence;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use Yii;

/**
 * @property int $id
 * @property int $telegram_id
 * @property string|null $username
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $status
 * @property bool $is_admin
 * @property string|null $password_hash
 * @property int $created_at
 * @property int $updated_at
 */
class UserRecord extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%users}}';
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
            [['telegram_id'], 'required'],
            [['telegram_id'], 'integer'],
            [['telegram_id'], 'unique'],
            [['is_admin'], 'boolean'],
            [['username', 'first_name', 'last_name', 'password_hash'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            [['status'], 'in', 'range' => ['active', 'blocked']],
        ];
    }

    /**
     * IdentityInterface methods
     */

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => 'active', 'is_admin' => true]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // Not used
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null; // Not used
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public static function findByTelegramId($telegramId)
    {
        return static::findOne(['telegram_id' => $telegramId, 'status' => 'active', 'is_admin' => true]);
    }

    public function validatePassword($password)
    {
        if (!$this->password_hash) {
            return false;
        }
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Relations
     */
    public function getProjects()
    {
        return $this->hasMany(ProjectRecord::class, ['user_id' => 'id']);
    }
}
