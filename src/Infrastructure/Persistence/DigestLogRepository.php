<?php

namespace app\Infrastructure\Persistence;

use app\Domain\Digest\DigestLog;
use app\Domain\Digest\DigestLogRepositoryInterface;

class DigestLogRepository implements DigestLogRepositoryInterface
{
    public function findById(int $id): ?DigestLog
    {
        $record = DigestLogRecord::findOne($id);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function save(DigestLog $log): void
    {
        if ($log->getId()) {
            $record = DigestLogRecord::findOne($log->getId());
        } else {
            $record = new DigestLogRecord();
        }

        $record->project_id = $log->getProjectId();
        $record->user_id = $log->getUserId();
        $record->matches_count = $log->getMatchesCount();
        $record->message_text = $log->getMessageText();
        $record->sent_at = $log->getSentAt();

        if (!$record->save()) {
            throw new \RuntimeException('Failed to save digest log');
        }

        if (!$log->getId()) {
            $reflection = new \ReflectionClass($log);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($log, $record->id);
        }
    }

    public function delete(DigestLog $log): void
    {
        if ($log->getId()) {
            DigestLogRecord::deleteAll(['id' => $log->getId()]);
        }
    }

    public function findByProjectId(int $projectId, int $limit = 50): array
    {
        $records = DigestLogRecord::find()
            ->where(['project_id' => $projectId])
            ->orderBy(['sent_at' => SORT_DESC])
            ->limit($limit)
            ->all();

        return array_map(fn($r) => $this->mapToDomain($r), $records);
    }

    public function findByUserId(int $userId, int $limit = 50): array
    {
        $records = DigestLogRecord::find()
            ->where(['user_id' => $userId])
            ->orderBy(['sent_at' => SORT_DESC])
            ->limit($limit)
            ->all();

        return array_map(fn($r) => $this->mapToDomain($r), $records);
    }

    private function mapToDomain(DigestLogRecord $record): DigestLog
    {
        return new DigestLog(
            projectId: $record->project_id,
            userId: $record->user_id,
            matchesCount: $record->matches_count,
            messageText: $record->message_text,
            sentAt: $record->sent_at,
            id: $record->id,
            createdAt: $record->created_at
        );
    }
}
