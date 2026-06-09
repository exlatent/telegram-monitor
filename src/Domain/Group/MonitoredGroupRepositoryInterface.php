<?php

namespace app\Domain\Group;

interface MonitoredGroupRepositoryInterface
{
    public function findById(int $id): ?MonitoredGroup;

    public function save(MonitoredGroup $group): void;

    public function delete(MonitoredGroup $group): void;

    /**
     * @param int $projectId
     * @return MonitoredGroup[]
     */
    public function findByProjectId(int $projectId): array;

    /**
     * @return MonitoredGroup[]
     */
    public function findAll(): array;

    public function findByTelegramUsername(string $username): ?MonitoredGroup;
}
