<?php

namespace app\Console\Controllers;

use app\Application\Digest\DigestService;
use app\Infrastructure\Persistence\DigestLogRepository;
use app\Infrastructure\Persistence\KeywordRepository;
use app\Infrastructure\Persistence\MatchRepository;
use app\Infrastructure\Persistence\MessageRepository;
use app\Infrastructure\Persistence\ProjectRepository;
use app\Infrastructure\Persistence\UserRepository;
use app\Infrastructure\Telegram\BotApiClient;
use yii\console\Controller;

class DigestController extends Controller
{
    /**
     * Запуск отправки дайджестов
     */
    public function actionRun(): void
    {
        echo "Starting digest sending process...\n";

        $botApiClient = new BotApiClient(\Yii::$app->params['telegram']['botToken']);

        $service = new DigestService(
            projectRepository: new ProjectRepository(),
            userRepository: new UserRepository(),
            matchRepository: new MatchRepository(),
            messageRepository: new MessageRepository(),
            keywordRepository: new KeywordRepository(),
            digestLogRepository: new DigestLogRepository(),
            botApiClient: $botApiClient
        );

        try {
            $service->runDigestSending();
            echo "Digest sending completed successfully\n";
        } catch (\Exception $e) {
            echo "Error during digest sending: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        }
    }
}
