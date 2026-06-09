<?php

namespace app\Domain\Message;

class Message
{
    private ?int $id;
    private int $groupId;
    private int $telegramMessageId;
    private ?string $text;
    private int $messageDate;
    private ?string $link;
    private int $createdAt;

    public function __construct(
        int $groupId,
        int $telegramMessageId,
        ?string $text,
        int $messageDate,
        ?string $link = null,
        ?int $id = null,
        ?int $createdAt = null
    ) {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->telegramMessageId = $telegramMessageId;
        $this->text = $text;
        $this->messageDate = $messageDate;
        $this->link = $link;
        $this->createdAt = $createdAt ?? time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getTelegramMessageId(): int
    {
        return $this->telegramMessageId;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getMessageDate(): int
    {
        return $this->messageDate;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function hasText(): bool
    {
        return !empty($this->text);
    }
}
