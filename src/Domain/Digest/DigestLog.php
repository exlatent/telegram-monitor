<?php

namespace app\Domain\Digest;

class DigestLog
{
    private ?int $id;
    private int $projectId;
    private int $userId;
    private int $matchesCount;
    private ?string $messageText;
    private int $sentAt;
    private int $createdAt;

    public function __construct(
        int $projectId,
        int $userId,
        int $matchesCount,
        ?string $messageText = null,
        ?int $sentAt = null,
        ?int $id = null,
        ?int $createdAt = null
    ) {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->matchesCount = $matchesCount;
        $this->messageText = $messageText;
        $this->sentAt = $sentAt ?? time();
        $this->createdAt = $createdAt ?? time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getMatchesCount(): int
    {
        return $this->matchesCount;
    }

    public function getMessageText(): ?string
    {
        return $this->messageText;
    }

    public function getSentAt(): int
    {
        return $this->sentAt;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }
}
