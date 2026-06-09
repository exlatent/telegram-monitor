<?php

namespace app\Domain\Keyword;

class Keyword
{
    private ?int $id;
    private int $projectId;
    private string $word;
    private array $normalizedForms;
    private int $createdAt;
    private int $updatedAt;

    public function __construct(
        int $projectId,
        string $word,
        array $normalizedForms = [],
        ?int $id = null,
        ?int $createdAt = null,
        ?int $updatedAt = null
    ) {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->word = $word;
        $this->normalizedForms = $normalizedForms;
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

    public function getWord(): string
    {
        return $this->word;
    }

    public function getNormalizedForms(): array
    {
        return $this->normalizedForms;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function setNormalizedForms(array $forms): void
    {
        $this->normalizedForms = $forms;
        $this->updatedAt = time();
    }

    public function matchesText(string $text): bool
    {
        // Простое вхождение слова
        if (mb_stripos($text, $this->word) !== false) {
            return true;
        }

        // Поиск по нормализованным формам
        foreach ($this->normalizedForms as $form) {
            if (mb_stripos($text, $form) !== false) {
                return true;
            }
        }

        return false;
    }
}
