<?php

namespace app\Infrastructure\Persistence;

use app\Domain\Group\MonitoredGroup;
use app\Domain\Group\MonitoredGroupRepositoryInterface;

class MonitoredGroupRepository implements MonitoredGroupRepositoryInterface
{
    public function findById(int $id): ?MonitoredGroup
    {
        $record = MonitoredGroupRecord::findOne($id);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function save(MonitoredGroup $group): void
    {
        if ($group->getId()) {
            $record = MonitoredGroupRecord::findOne($group->getId());
        } else {
            $record = new MonitoredGroupRecord();
        }

        $record->project_id = $group->getProjectId();
        $record->telegram_username = $group->getTelegramUsername();
        $record->telegram_id = $group->getTelegramId();
        $record->name = $group->getName();
        $record->last_message_id = $group->getLastMessageId();

        if (!$record->save()) {
            throw new \RuntimeException('Failed to save group');
        }

        if (!$group->getId()) {
            $reflection = new \ReflectionClass($group);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($group, $record->id);
        }
    }

    public function delete(MonitoredGroup $group): void
    {
        if ($group->getId()) {
            MonitoredGroupRecord::deleteAll(['id' => $group->getId()]);
        }
    }

    public function findByProjectId(int $projectId): array
    {
        $records = MonitoredGroupRecord::find()->where(['project_id' => $projectId])->all();
        return array_map(fn($r) => $this->mapToDomain($r), $records);
    }

    public function findAll(): array
    {
        $records = MonitoredGroupRecord::find()->all();
        return array_map(fn($r) => $this->mapToDomain($r), $records);
    }

    public function findByTelegramUsername(string $username): ?MonitoredGroup
    {
        $record = MonitoredGroupRecord::findOne(['telegram_username' => $username]);
        return $record ? $this->mapToDomain($record) : null;
    }

    private function mapToDomain(MonitoredGroupRecord $record): MonitoredGroup
    {
        return new MonitoredGroup(
            projectId: $record->project_id,
            name: $record->name,
            telegramUsername: $record->telegram_username,
            telegramId: $record->telegram_id,
            lastMessageId: $record->last_message_id,
            id: $record->id,
            createdAt: $record->created_at,
            updatedAt: $record->updated_at
        );
    }
}
