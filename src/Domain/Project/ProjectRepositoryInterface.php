<?php

namespace app\Domain\Project;

interface ProjectRepositoryInterface
{
    public function findById(int $id): ?Project;

    public function save(Project $project): void;

    public function delete(Project $project): void;

    /**
     * @param int $userId
     * @return Project[]
     */
    public function findByUserId(int $userId): array;

    /**
     * @return Project[]
     */
    public function findActive(): array;

    /**
     * @return Project[]
     */
    public function findReadyForDigest(): array;
}
