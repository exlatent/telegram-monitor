<?php

namespace app\Infrastructure\Persistence;

use app\Domain\User\User;
use app\Domain\User\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $record = UserRecord::findOne($id);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function findByTelegramId(int $telegramId): ?User
    {
        $record = UserRecord::findOne(['telegram_id' => $telegramId]);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function save(User $user): void
    {
        if ($user->getId()) {
            $record = UserRecord::findOne($user->getId());
            if (!$record) {
                throw new \RuntimeException('User not found');
            }
        } else {
            $record = new UserRecord();
        }

        $record->telegram_id = $user->getTelegramId();
        $record->username = $user->getUsername();
        $record->first_name = $user->getFirstName();
        $record->last_name = $user->getLastName();
        $record->status = $user->getStatus();

        if (!$record->save()) {
            throw new \RuntimeException('Failed to save user: ' . json_encode($record->errors));
        }

        // Update domain entity with generated ID
        if (!$user->getId()) {
            $reflection = new \ReflectionClass($user);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($user, $record->id);
        }
    }

    public function delete(User $user): void
    {
        if ($user->getId()) {
            $record = UserRecord::findOne($user->getId());
            if ($record) {
                $record->delete();
            }
        }
    }

    public function findAll(): array
    {
        $records = UserRecord::find()->all();
        return array_map(fn($record) => $this->mapToDomain($record), $records);
    }

    public function findByStatus(string $status): array
    {
        $records = UserRecord::find()->where(['status' => $status])->all();
        return array_map(fn($record) => $this->mapToDomain($record), $records);
    }

    private function mapToDomain(UserRecord $record): User
    {
        return new User(
            telegramId: $record->telegram_id,
            username: $record->username,
            firstName: $record->first_name,
            lastName: $record->last_name,
            status: $record->status,
            id: $record->id,
            createdAt: $record->created_at,
            updatedAt: $record->updated_at
        );
    }
}
