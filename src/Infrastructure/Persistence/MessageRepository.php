<?php

namespace app\Infrastructure\Persistence;

use app\Domain\Message\Message;
use app\Domain\Message\MessageRepositoryInterface;

class MessageRepository implements MessageRepositoryInterface
{
    public function findById(int $id): ?Message
    {
        $record = MessageRecord::findOne($id);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function save(Message $message): void
    {
        if ($message->getId()) {
            $record = MessageRecord::findOne($message->getId());
        } else {
            $record = new MessageRecord();
        }

        $record->group_id = $message->getGroupId();
        $record->telegram_message_id = $message->getTelegramMessageId();
        $record->text = $message->getText();
        $record->message_date = $message->getMessageDate();
        $record->link = $message->getLink();

        if (!$record->save()) {
            throw new \RuntimeException('Failed to save message');
        }

        if (!$message->getId()) {
            $reflection = new \ReflectionClass($message);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($message, $record->id);
        }
    }

    public function delete(Message $message): void
    {
        if ($message->getId()) {
            MessageRecord::deleteAll(['id' => $message->getId()]);
        }
    }

    public function findByGroupAndTelegramId(int $groupId, int $telegramMessageId): ?Message
    {
        $record = MessageRecord::findOne([
            'group_id' => $groupId,
            'telegram_message_id' => $telegramMessageId
        ]);
        return $record ? $this->mapToDomain($record) : null;
    }

    public function findByGroupId(int $groupId, int $limit = 100): array
    {
        $records = MessageRecord::find()
            ->where(['group_id' => $groupId])
            ->orderBy(['message_date' => SORT_DESC])
            ->limit($limit)
            ->all();

        return array_map(fn($r) => $this->mapToDomain($r), $records);
    }

    private function mapToDomain(MessageRecord $record): Message
    {
        return new Message(
            groupId: $record->group_id,
            telegramMessageId: $record->telegram_message_id,
            text: $record->text,
            messageDate: $record->message_date,
            link: $record->link,
            id: $record->id,
            createdAt: $record->created_at
        );
    }
}
