<?php

namespace app\Domain\Keyword;

interface KeywordRepositoryInterface
{
    public function findById(int $id): ?Keyword;

    public function save(Keyword $keyword): void;

    public function delete(Keyword $keyword): void;

    /**
     * @param int $projectId
     * @return Keyword[]
     */
    public function findByProjectId(int $projectId): array;
}
