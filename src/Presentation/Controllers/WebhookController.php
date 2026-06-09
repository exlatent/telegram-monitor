<?php

namespace app\Presentation\Controllers;

use app\Application\Bot\BotHandler;
use app\Infrastructure\Morphology\MorphologyAnalyzer;
use app\Infrastructure\Persistence\ProjectRepository;
use app\Infrastructure\Persistence\UserRepository;
use app\Infrastructure\Telegram\BotApiClient;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class WebhookController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Обработка webhook от Telegram
     */
    public function actionIndex(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $update = json_decode(file_get_contents('php://input'), true);

        if (!$update) {
            return ['ok' => false, 'error' => 'No update'];
        }

        try {
            $botToken = Yii::$app->params['telegram']['botToken'] ?? null;
            if (!$botToken) {
                throw new \RuntimeException('Bot token not configured');
            }

            $botApiClient = new BotApiClient($botToken);

            $handler = new BotHandler(
                userRepository: new UserRepository(),
                projectRepository: new ProjectRepository(),
                botApiClient: $botApiClient,
                morphologyAnalyzer: new MorphologyAnalyzer()
            );

            $handler->handleUpdate($update);

            return ['ok' => true];
        } catch (\Exception $e) {
            Yii::error('Webhook error: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        }
    }
}
