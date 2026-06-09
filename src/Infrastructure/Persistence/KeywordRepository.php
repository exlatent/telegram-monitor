<?php

namespace app\Infrastructure\Persistence;

use app\Domain\Keyword\Keyword;
use app\Domain\Keyword\KeywordRepositoryInterface;

class KeywordRepository implements KeywordRepositoryInterface
{
    public function findById(int $id): ?Keyword
    {
        $record = KeywordRecord::findOne($id);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function save(Keyword $keyword): void
    {
        if ($keyword->getId()) {
            $record = KeywordRecord::findOne($keyword->getId());
        } else {
            $record = new KeywordRecord();
        }

        $record->project_id = $keyword->getProjectId();
        $record->word = $keyword->getWord();
        $record->setNormalizedFormsArray($keyword->getNormalizedForms());

        if (!$record->save()) {
            throw new \RuntimeException('Failed to save keyword');
        }

        if (!$keyword->getId()) {
            $reflection = new \ReflectionClass($keyword);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($keyword, $record->id);
        }
    }

    public function delete(Keyword $keyword): void
    {
        if ($keyword->getId()) {
            KeywordRecord::deleteAll(['id' => $keyword->getId()]);
        }
    }

    public function findByProjectId(int $projectId): array
    {
        $records = KeywordRecord::find()->where(['project_id' => $projectId])->all();
        return array_map(fn($r) => $this->mapToDomain($r), $records);
    }

    private function mapToDomain(KeywordRecord $record): Keyword
    {
        return new Keyword(
            projectId: $record->project_id,
            word: $record->word,
            normalizedForms: $record->getNormalizedFormsArray(),
            id: $record->id,
            createdAt: $record->created_at,
            updatedAt: $record->updated_at
        );
    }
}
