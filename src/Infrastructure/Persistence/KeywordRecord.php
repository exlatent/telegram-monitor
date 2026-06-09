<?php

namespace app\Infrastructure\Persistence;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;

/**
 * @property int $id
 * @property int $project_id
 * @property string $word
 * @property string|null $normalized_forms
 * @property int $created_at
 * @property int $updated_at
 */
class KeywordRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%keywords}}';
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
            [['project_id', 'word'], 'required'],
            [['project_id'], 'integer'],
            [['word'], 'string', 'max' => 255],
            [['normalized_forms'], 'safe'],
        ];
    }

    public function getProject()
    {
        return $this->hasOne(ProjectRecord::class, ['id' => 'project_id']);
    }

    public function getNormalizedFormsArray(): array
    {
        return $this->normalized_forms ? Json::decode($this->normalized_forms) : [];
    }

    public function setNormalizedFormsArray(array $forms): void
    {
        $this->normalized_forms = Json::encode($forms);
    }
}
