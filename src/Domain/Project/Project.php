<?php

namespace app\Domain\Project;

class Project
{
    private ?int $id;
    private int $userId;
    private string $name;
    private int $digestInterval;
    private bool $isActive;
    private ?int $lastDigestSentAt;
    private int $createdAt;
    private int $updatedAt;

    public function __construct(
        int $userId,
        string $name,
        int $digestInterval,
        bool $isActive = true,
        ?int $id = null,
        ?int $lastDigestSentAt = null,
        ?int $createdAt = null,
        ?int $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->name = $name;
        $this->digestInterval = $digestInterval;
        $this->isActive = $isActive;
        $this->lastDigestSentAt = $lastDigestSentAt;
        $this->createdAt = $createdAt ?? time();
        $this->updatedAt = $updatedAt ?? time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDigestInterval(): int
    {
        return $this->digestInterval;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getLastDigestSentAt(): ?int
    {
        return $this->lastDigestSentAt;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = time();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = time();
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = time();
    }

    public function updateDigestInterval(int $minutes): void
    {
        $this->digestInterval = $minutes;
        $this->updatedAt = time();
    }

    public function markDigestSent(): void
    {
        $this->lastDigestSentAt = time();
        $this->updatedAt = time();
    }

    public function shouldSendDigest(): bool
    {
        if (!$this->isActive) {
            return false;
        }

        if ($this->lastDigestSentAt === null) {
            return true;
        }

        $intervalSeconds = $this->digestInterval * 60;
        return (time() - $this->lastDigestSentAt) >= $intervalSeconds;
    }
}
