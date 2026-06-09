<?php

namespace app\Console\Controllers;

use app\Application\Monitor\MonitorService;
use app\Infrastructure\Morphology\MorphologyAnalyzer;
use app\Infrastructure\Persistence\KeywordRepository;
use app\Infrastructure\Persistence\MatchRepository;
use app\Infrastructure\Persistence\MessageRepository;
use app\Infrastructure\Persistence\MonitoredGroupRepository;
use app\Infrastructure\Persistence\ProjectRepository;
use app\Infrastructure\Telegram\MTProtoClient;
use yii\console\Controller;

class MonitorController extends Controller
{
    /**
     * Запуск мониторинга всех групп
     */
    public function actionRun(): void
    {
        echo "Starting monitoring process...\n";

        $mtProtoClient = new MTProtoClient([
            'app_id'       => \Yii::$app->params['telegram']['mtproto']['app_id'],
            'app_hash'     => \Yii::$app->params['telegram']['mtproto']['app_hash'],
            'session_file' => \Yii::getAlias('@runtime/session.madeline'),
        ]);

        // Инициализируем MTProto
        try {
            $mtProtoClient->start();
        } catch (\Exception $e) {
            echo "Error starting MTProto client: " . $e->getMessage() . "\n";
            return;
        }

        $service = new MonitorService(
            groupRepository: new MonitoredGroupRepository(),
            messageRepository: new MessageRepository(),
            keywordRepository: new KeywordRepository(),
            matchRepository: new MatchRepository(),
            projectRepository: new ProjectRepository(),
            mtProtoClient: $mtProtoClient,
            morphologyAnalyzer: new MorphologyAnalyzer()
        );

        try {
            $service->runMonitoring();
            echo "Monitoring completed successfully\n";
        } catch (\Exception $e) {
            echo "Error during monitoring: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        }
    }
}
