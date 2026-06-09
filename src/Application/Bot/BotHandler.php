<?php

namespace app\Application\Bot;

use app\Domain\User\User;
use app\Domain\User\UserRepositoryInterface;
use app\Domain\Project\ProjectRepositoryInterface;
use app\Infrastructure\Telegram\BotApiClient;
use app\Infrastructure\Morphology\MorphologyAnalyzer;
use Yii;

class BotHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly BotApiClient $botApiClient,
        private readonly MorphologyAnalyzer $morphologyAnalyzer
    ) {}

    public function handleUpdate(array $update): void
    {
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
    }

    private function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $from = $message['from'] ?? [];

        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $text, $from);
            return;
        }
    }

    private function handleCommand(int $chatId, string $command, array $from): void
    {
        $parts = explode(' ', $command);
        $cmd = $parts[0];

        switch ($cmd) {
            case '/start':
                $this->handleStart($chatId, $from);
                break;
            case '/help':
                $this->handleHelp($chatId);
                break;
            default:
                // Не отвечаем на неизвестные команды, чтобы не забивать чат
                break;
        }
    }

    private function handleStart(int $chatId, array $from): void
    {
        $telegramId = $from['id'];
        $user = $this->userRepository->findByTelegramId($telegramId);

        if (!$user) {
            $user = new User(
                telegramId: $telegramId,
                username: $from['username'] ?? null,
                firstName: $from['first_name'] ?? null,
                lastName: $from['last_name'] ?? null
            );
            $this->userRepository->save($user);
        }

        $webAppUrl = Yii::$app->params['telegram']['webAppUrl'] ?? '';
        $webAppUrl = trim($webAppUrl);

        $message = "👋 <b>Добро пожаловать в Message Radar!</b>\n\n";
        $message .= "Я помогу вам отслеживать ключевые слова в публичных группах.\n\n";
        $message .= "Нажмите кнопку ниже, чтобы настроить ваши радары 👇";

        $replyMarkup = [
            'inline_keyboard' => [
                [
                    [
                        'text' => '🚀 Открыть управление',
                        'web_app' => ['url' => $webAppUrl]
                    ]
                ]
            ]
        ];

        $this->botApiClient->sendMessage($chatId, $message, $replyMarkup);
    }

    private function handleHelp(int $chatId): void
    {
        $message = "📚 <b>Справка:</b>\n\nВсе настройки выполняются через встроенное приложение. Нажмите на кнопку «Открыть управление».";
        $this->botApiClient->sendMessage($chatId, $message);
    }

    private function handleCallbackQuery(array $callbackQuery): void
    {
        $this->botApiClient->answerCallbackQuery($callbackQuery['id']);
    }
}
