<?php

namespace app\Domain\Message;

interface MessageRepositoryInterface
{
    public function findById(int $id): ?Message;

    public function save(Message $message): void;

    public function delete(Message $message): void;

    /**
     * @param int $groupId
     * @param int $telegramMessageId
     * @return Message|null
     */
    public function findByGroupAndTelegramId(int $groupId, int $telegramMessageId): ?Message;

    /**
     * @param int $groupId
     * @param int $limit
     * @return Message[]
     */
    public function findByGroupId(int $groupId, int $limit = 100): array;
}
