<?php

namespace app\Domain\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByTelegramId(int $telegramId): ?User;

    public function save(User $user): void;

    public function delete(User $user): void;

    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param string $status
     * @return User[]
     */
    public function findByStatus(string $status): array;
}
