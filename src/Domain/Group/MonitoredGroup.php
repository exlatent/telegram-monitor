<?php

namespace app\Domain\Group;

class MonitoredGroup
{
    private ?int $id;
    private int $projectId;
    private ?string $telegramUsername;
    private ?int $telegramId;
    private string $name;
    private ?int $lastMessageId;
    private int $createdAt;
    private int $updatedAt;

    public function __construct(
        int $projectId,
        string $name,
        ?string $telegramUsername = null,
        ?int $telegramId = null,
        ?int $lastMessageId = null,
        ?int $id = null,
        ?int $createdAt = null,
        ?int $updatedAt = null
    ) {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->telegramUsername = $telegramUsername;
        $this->telegramId = $telegramId;
        $this->name = $name;
        $this->lastMessageId = $lastMessageId;
        $this->createdAt = $createdAt ?? time();
        $this->updatedAt = $updatedAt ?? time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getTelegramUsername(): ?string
    {
        return $this->telegramUsername;
    }

    public function getTelegramId(): ?int
    {
        return $this->telegramId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastMessageId(): ?int
    {
        return $this->lastMessageId;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function updateTelegramId(int $telegramId): void
    {
        $this->telegramId = $telegramId;
        $this->updatedAt = time();
    }

    public function updateLastMessageId(int $messageId): void
    {
        $this->lastMessageId = $messageId;
        $this->updatedAt = time();
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = time();
    }

    public function getIdentifier(): string
    {
        return $this->telegramUsername ?? (string)$this->telegramId;
    }
}
