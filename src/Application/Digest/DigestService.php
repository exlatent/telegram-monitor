<?php

namespace app\Application\Digest;

use app\Domain\Project\ProjectRepositoryInterface;
use app\Domain\User\UserRepositoryInterface;
use app\Domain\Match\MatchRepositoryInterface;
use app\Domain\Message\MessageRepositoryInterface;
use app\Domain\Keyword\KeywordRepositoryInterface;
use app\Domain\Digest\DigestLog;
use app\Domain\Digest\DigestLogRepositoryInterface;
use app\Infrastructure\Telegram\BotApiClient;
use Psr\Log\LoggerInterface;

class DigestService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private UserRepositoryInterface $userRepository,
        private MatchRepositoryInterface $matchRepository,
        private MessageRepositoryInterface $messageRepository,
        private KeywordRepositoryInterface $keywordRepository,
        private DigestLogRepositoryInterface $digestLogRepository,
        private BotApiClient $botApiClient,
        private ?LoggerInterface $logger = null
    ) {}

    /**
     * Запуск отправки дайджестов
     */
    public function runDigestSending(): void
    {
        $this->log('Starting digest sending...');

        // Получаем проекты, готовые к отправке дайджеста
        $projects = $this->projectRepository->findReadyForDigest();

        if (empty($projects)) {
            $this->log('No projects ready for digest');
            return;
        }

        $this->log(sprintf('Found %d projects ready for digest', count($projects)));

        foreach ($projects as $project) {
            try {
                $this->sendDigestForProject($project);
            } catch (\Exception $e) {
                $this->log(sprintf('Error sending digest for project %s: %s', $project->getName(), $e->getMessage()));
            }
        }

        $this->log('Digest sending completed');
    }

    /**
     * Отправка дайджеста для проекта
     */
    private function sendDigestForProject($project): void
    {
        $this->log(sprintf('Processing digest for project: %s', $project->getName()));

        // Получаем неотправленные совпадения
        $matches = $this->matchRepository->findUnsentByProjectId($project->getId());

        if (empty($matches)) {
            $this->log(sprintf('No unsent matches for project %s', $project->getName()));
            return;
        }

        $this->log(sprintf('Found %d unsent matches for project %s', count($matches), $project->getName()));

        // Получаем пользователя
        $user = $this->userRepository->findById($project->getUserId());

        if (!$user || !$user->isActive()) {
            $this->log(sprintf('User not found or inactive for project %s', $project->getName()));
            return;
        }

        // Формируем текст дайджеста
        $digestText = $this->buildDigestMessage($project, $matches);

        // Отправляем дайджест
        try {
            $this->botApiClient->sendMessage($user->getTelegramId(), $digestText);

            // Помечаем совпадения как отправленные
            foreach ($matches as $match) {
                $match->markAsSent();
                $this->matchRepository->save($match);
            }

            // Обновляем время последней отправки дайджеста
            $project->markDigestSent();
            $this->projectRepository->save($project);

            // Сохраняем лог отправки
            $digestLog = new DigestLog(
                projectId: $project->getId(),
                userId: $user->getId(),
                matchesCount: count($matches),
                messageText: mb_substr($digestText, 0, 1000) // Сохраняем первые 1000 символов
            );
            $this->digestLogRepository->save($digestLog);

            $this->log(sprintf('Digest sent successfully for project %s (%d matches)', $project->getName(), count($matches)));
        } catch (\Exception $e) {
            $this->log(sprintf('Failed to send digest for project %s: %s', $project->getName(), $e->getMessage()));
            throw $e;
        }
    }

    /**
     * Формирование текста дайджеста
     */
    private function buildDigestMessage($project, array $matches): string
    {
        $text = "📊 <b>Дайджест проекта \"{$project->getName()}\"</b>\n\n";
        $text .= sprintf("Найдено совпадений: <b>%d</b>\n\n", count($matches));

        $groupedByKeyword = [];

        foreach ($matches as $match) {
            $keyword = $this->keywordRepository->findById($match->getKeywordId());
            $message = $this->messageRepository->findById($match->getMessageId());

            if (!$keyword || !$message) {
                continue;
            }

            $keywordWord = $keyword->getWord();

            if (!isset($groupedByKeyword[$keywordWord])) {
                $groupedByKeyword[$keywordWord] = [];
            }

            $groupedByKeyword[$keywordWord][] = [
                'message' => $message,
                'match' => $match,
            ];
        }

        foreach ($groupedByKeyword as $keywordWord => $items) {
            $text .= "🔍 <b>Ключевое слово:</b> {$keywordWord}\n";

            foreach ($items as $item) {
                $message = $item['message'];

                // Убираем переносы строк и обрезаем текст сообщения
                $cleanText = str_replace(["\r", "\n"], ' ', $message->getText());

                $messageText = mb_substr($cleanText, 0, 50);
                if (mb_strlen($cleanText) > 50) {
                    $messageText .= '...';
                }

                $messageText = htmlspecialchars($messageText, ENT_QUOTES, 'UTF-8');

                if ($message->getLink()) {
                    $text .= sprintf("• <a href=\"%s\">%s</a>\n", $message->getLink(), $messageText);
                } else {
                    $text .= sprintf("• %s\n", $messageText);
                }
            }

            $text .= "\n";
        }

        return $text;
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
