<?php

namespace app\Domain\User;

class User
{
    private ?int $id;
    private int $telegramId;
    private ?string $username;
    private ?string $firstName;
    private ?string $lastName;
    private string $status;
    private int $createdAt;
    private int $updatedAt;

    public function __construct(
        int $telegramId,
        ?string $username = null,
        ?string $firstName = null,
        ?string $lastName = null,
        string $status = UserStatus::ACTIVE,
        ?int $id = null,
        ?int $createdAt = null,
        ?int $updatedAt = null
    ) {
        $this->id = $id;
        $this->telegramId = $telegramId;
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->status = $status;
        $this->createdAt = $createdAt ?? time();
        $this->updatedAt = $updatedAt ?? time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelegramId(): int
    {
        return $this->telegramId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function block(): void
    {
        $this->status = UserStatus::BLOCKED;
        $this->updatedAt = time();
    }

    public function activate(): void
    {
        $this->status = UserStatus::ACTIVE;
        $this->updatedAt = time();
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function updateInfo(?string $username, ?string $firstName, ?string $lastName): void
    {
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->updatedAt = time();
    }
}
