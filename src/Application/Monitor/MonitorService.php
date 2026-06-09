<?php

namespace app\Application\Monitor;

use app\Domain\Group\MonitoredGroupRepositoryInterface;
use app\Domain\Keyword\KeywordRepositoryInterface;
use app\Domain\Message\Message;
use app\Domain\Message\MessageRepositoryInterface;
use app\Domain\Match\WordMatch;
use app\Domain\Match\MatchRepositoryInterface;
use app\Domain\Project\ProjectRepositoryInterface;
use app\Infrastructure\Telegram\MTProtoClient;
use app\Infrastructure\Morphology\MorphologyAnalyzer;
use Psr\Log\LoggerInterface;

class MonitorService
{
    public function __construct(
        private MonitoredGroupRepositoryInterface $groupRepository,
        private MessageRepositoryInterface $messageRepository,
        private KeywordRepositoryInterface $keywordRepository,
        private MatchRepositoryInterface $matchRepository,
        private ProjectRepositoryInterface $projectRepository,
        private MTProtoClient $mtProtoClient,
        private MorphologyAnalyzer $morphologyAnalyzer,
        private ?LoggerInterface $logger = null
    ) {}

    /**
     * Запуск мониторинга всех групп
     */
    public function runMonitoring(): void
    {
        $this->log('Starting monitoring...');

        // Получаем все активные проекты
        $activeProjects = $this->projectRepository->findActive();

        if (empty($activeProjects)) {
            $this->log('No active projects found');
            return;
        }

        // Получаем все отслеживаемые группы
        $groups = $this->groupRepository->findAll();

        if (empty($groups)) {
            $this->log('No monitored groups found');
            return;
        }

        $this->log(sprintf('Found %d groups to monitor', count($groups)));

        foreach ($groups as $group) {
            try {
                $this->monitorGroup($group);
            } catch (\Exception $e) {
                $this->log(sprintf('Error monitoring group %s: %s', $group->getName(), $e->getMessage()));
            }
        }

        $this->log('Monitoring completed');
    }

    /**
     * Мониторинг одной группы
     */
    private function monitorGroup($group): void
    {
        $this->log(sprintf('Monitoring group: %s', $group->getName()));

        $identifier = $group->getIdentifier();
        $lastMessageId = $group->getLastMessageId() ?? 0;

        // Получаем новые сообщения
        $newMessages = $this->mtProtoClient->getNewMessages($identifier, $lastMessageId, 100);

        if (empty($newMessages)) {
            $this->log(sprintf('No new messages in group %s', $group->getName()));
            return;
        }

        $this->log(sprintf('Found %d new messages in group %s', count($newMessages), $group->getName()));

        $maxMessageId = $lastMessageId;

        foreach ($newMessages as $telegramMessage) {
            $messageId = $this->mtProtoClient->extractMessageId($telegramMessage);
            $text = $this->mtProtoClient->extractMessageText($telegramMessage);

            // Пропускаем сообщения без текста
            if (empty($text)) {
                continue;
            }

            // Создаём доменную сущность Message
            $message = new Message(
                groupId: $group->getId(),
                telegramMessageId: $messageId,
                text: $text,
                messageDate: $this->mtProtoClient->extractMessageDate($telegramMessage),
                link: $group->getTelegramUsername()
                    ? $this->mtProtoClient->getMessageLink($group->getTelegramUsername(), $messageId)
                    : null
            );

            // Проверяем, не сохранено ли уже это сообщение
            if (!$this->messageRepository->findByGroupAndTelegramId($group->getId(), $messageId)) {
                $this->messageRepository->save($message);
                $this->log(sprintf('Saved message #%d from group %s', $messageId, $group->getName()));

                // Проверяем совпадения с ключевыми словами
                $this->checkMatches($message, $group->getProjectId());
            }

            if ($messageId > $maxMessageId) {
                $maxMessageId = $messageId;
            }
        }

        // Обновляем last_message_id
        if ($maxMessageId > $lastMessageId) {
            $group->updateLastMessageId($maxMessageId);
            $this->groupRepository->save($group);
            $this->log(sprintf('Updated last_message_id to %d for group %s', $maxMessageId, $group->getName()));
        }
    }

    /**
     * Проверка совпадений с ключевыми словами
     */
    private function checkMatches(Message $message, int $projectId): void
    {
        $keywords = $this->keywordRepository->findByProjectId($projectId);

        if (empty($keywords)) {
            return;
        }

        foreach ($keywords as $keyword) {
            // Проверяем вхождение с учётом морфологии
            if ($this->morphologyAnalyzer->matchInText($keyword->getNormalizedForms(), $message->getText())) {
                // Проверяем, не создан ли уже такой match
                if (!$this->matchRepository->findByMessageKeywordProject($message->getId(), $keyword->getId(), $projectId)) {
                    $match = new WordMatch(
                        messageId: $message->getId(),
                        keywordId: $keyword->getId(),
                        projectId: $projectId
                    );

                    $this->matchRepository->save($match);
                    $this->log(sprintf('Created match for keyword "%s" in message #%d', $keyword->getWord(), $message->getTelegramMessageId()));
                }
            }
        }
    }

    private function log(string $message): void
    {
        if ($this->logger) {
            $this->logger->info($message);
        } else {
            echo "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
        }
    }
}
