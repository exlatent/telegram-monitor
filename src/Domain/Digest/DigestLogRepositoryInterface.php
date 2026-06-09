<?php

namespace app\Domain\Digest;

interface DigestLogRepositoryInterface
{
    public function findById(int $id): ?DigestLog;

    public function save(DigestLog $log): void;

    public function delete(DigestLog $log): void;

    /**
     * @param int $projectId
     * @param int $limit
     * @return DigestLog[]
     */
    public function findByProjectId(int $projectId, int $limit = 50): array;

    /**
     * @param int $userId
     * @param int $limit
     * @return DigestLog[]
     */
    public function findByUserId(int $userId, int $limit = 50): array;
}
