<?php

namespace app\Infrastructure\Persistence;

use app\Domain\Project\Project;
use app\Domain\Project\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function findById(int $id): ?Project
    {
        $record = ProjectRecord::findOne($id);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function save(Project $project): void
    {
        if ($project->getId()) {
            $record = ProjectRecord::findOne($project->getId());
            if (!$record) {
                throw new \RuntimeException('Project not found');
            }
        } else {
            $record = new ProjectRecord();
        }

        $record->user_id = $project->getUserId();
        $record->name = $project->getName();
        $record->digest_interval = $project->getDigestInterval();
        $record->is_active = $project->isActive();
        $record->last_digest_sent_at = $project->getLastDigestSentAt();

        if (!$record->save()) {
            throw new \RuntimeException('Failed to save project: ' . json_encode($record->errors));
        }

        if (!$project->getId()) {
            $reflection = new \ReflectionClass($project);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($project, $record->id);
        }
    }

    public function delete(Project $project): void
    {
        if ($project->getId()) {
            $record = ProjectRecord::findOne($project->getId());
            if ($record) {
                $record->delete();
            }
        }
    }

    public function findByUserId(int $userId): array
    {
        $records = ProjectRecord::find()->where(['user_id' => $userId])->all();
        return array_map(fn($record) => $this->mapToDomain($record), $records);
    }

    public function findActive(): array
    {
        $records = ProjectRecord::find()->where(['is_active' => true])->all();
        return array_map(fn($record) => $this->mapToDomain($record), $records);
    }

    public function findReadyForDigest(): array
    {
        $now = time();
        $records = ProjectRecord::find()
            ->where(['is_active' => true])
            ->andWhere([
                'or',
                ['last_digest_sent_at' => null],
                ['<', 'last_digest_sent_at + (digest_interval * 60)', $now]
            ])
            ->all();

        return array_map(fn($record) => $this->mapToDomain($record), $records);
    }

    private function mapToDomain(ProjectRecord $record): Project
    {
        return new Project(
            userId: $record->user_id,
            name: $record->name,
            digestInterval: $record->digest_interval,
            isActive: (bool)$record->is_active,
            id: $record->id,
            lastDigestSentAt: $record->last_digest_sent_at,
            createdAt: $record->created_at,
            updatedAt: $record->updated_at
        );
    }
}
