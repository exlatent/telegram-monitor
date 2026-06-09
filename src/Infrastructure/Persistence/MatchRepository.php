<?php

namespace app\Infrastructure\Persistence;

use app\Domain\Match\WordMatch;
use app\Domain\Match\MatchRepositoryInterface;

class MatchRepository implements MatchRepositoryInterface
{
    public function findById(int $id): ?WordMatch
    {
        $record = MatchRecord::findOne($id);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function save(WordMatch $match): void
    {
        if ($match->getId()) {
            $record = MatchRecord::findOne($match->getId());
        } else {
            $record = new MatchRecord();
        }

        $record->message_id = $match->getMessageId();
        $record->keyword_id = $match->getKeywordId();
        $record->project_id = $match->getProjectId();
        $record->is_sent = $match->isSent();
        $record->sent_at = $match->getSentAt();

        if (!$record->save()) {
            throw new \RuntimeException('Failed to save match');
        }

        if (!$match->getId()) {
            $reflection = new \ReflectionClass($match);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($match, $record->id);
        }
    }

    public function delete(WordMatch $match): void
    {
        if ($match->getId()) {
            MatchRecord::deleteAll(['id' => $match->getId()]);
        }
    }

    public function findByMessageKeywordProject(int $messageId, int $keywordId, int $projectId): ?WordMatch
    {
        $record = MatchRecord::findOne([
            'message_id' => $messageId,
            'keyword_id' => $keywordId,
            'project_id' => $projectId
        ]);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function findUnsentByProjectId(int $projectId): array
    {
        $records = MatchRecord::find()
            ->where(['project_id' => $projectId, 'is_sent' => false])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        return array_map(fn($r) => $this->mapToDomain($r), $records);
    }

    public function findByProjectId(int $projectId, int $limit = 100): array
    {
        $records = MatchRecord::find()
            ->where(['project_id' => $projectId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();

        return array_map(fn($r) => $this->mapToDomain($r), $records);
    }

    private function mapToDomain(MatchRecord $record): WordMatch
    {
        return new WordMatch(
            messageId: $record->message_id,
            keywordId: $record->keyword_id,
            projectId: $record->project_id,
            isSent: (bool)$record->is_sent,
            sentAt: $record->sent_at,
            id: $record->id,
            createdAt: $record->created_at
        );
    }
}
