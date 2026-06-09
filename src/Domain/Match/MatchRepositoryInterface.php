<?php

namespace app\Domain\Match;

interface MatchRepositoryInterface
{
    public function findById(int $id): ?WordMatch;

    public function save(WordMatch $match): void;

    public function delete(WordMatch $match): void;

    /**
     * @param int $messageId
     * @param int $keywordId
     * @param int $projectId
     * @return WordMatch|null
     */
    public function findByMessageKeywordProject(int $messageId, int $keywordId, int $projectId): ?WordMatch;

    /**
     * @param int $projectId
     * @return WordMatch[]
     */
    public function findUnsentByProjectId(int $projectId): array;

    /**
     * @param int $projectId
     * @param int $limit
     * @return WordMatch[]
     */
    public function findByProjectId(int $projectId, int $limit = 100): array;
}
