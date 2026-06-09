<?php

namespace app\Domain\Match;

class WordMatch
{
    private ?int $id;
    private int $messageId;
    private int $keywordId;
    private int $projectId;
    private bool $isSent;
    private ?int $sentAt;
    private int $createdAt;

    public function __construct(
        int $messageId,
        int $keywordId,
        int $projectId,
        bool $isSent = false,
        ?int $sentAt = null,
        ?int $id = null,
        ?int $createdAt = null
    ) {
        $this->id = $id;
        $this->messageId = $messageId;
        $this->keywordId = $keywordId;
        $this->projectId = $projectId;
        $this->isSent = $isSent;
        $this->sentAt = $sentAt;
        $this->createdAt = $createdAt ?? time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getKeywordId(): int
    {
        return $this->keywordId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function isSent(): bool
    {
        return $this->isSent;
    }

    public function getSentAt(): ?int
    {
        return $this->sentAt;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function markAsSent(): void
    {
        $this->isSent = true;
        $this->sentAt = time();
    }
}
