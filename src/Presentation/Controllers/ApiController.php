<?php

namespace app\Presentation\Controllers;

use app\Infrastructure\Morphology\MorphologyAnalyzer;
use app\Infrastructure\Persistence\KeywordRecord;
use app\Infrastructure\Persistence\MonitoredGroupRecord;
use app\Infrastructure\Persistence\ProjectRecord;
use app\Infrastructure\Persistence\UserRecord;
use app\Infrastructure\Telegram\MTProtoClient;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    private ?MTProtoClient $_mtProtoClient = null; // Свойство для хранения экземпляра MTProtoClient

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => false,
                ],
            ],
        ]);
    }

    public function init()
    {
        parent::init();
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    private function getMTProtoClient(): MTProtoClient
    {
        if ($this->_mtProtoClient === null) {
            $this->_mtProtoClient = new MTProtoClient([
                'app_id' => Yii::$app->params['telegram']['mtproto']['app_id'],
                'app_hash' => Yii::$app->params['telegram']['mtproto']['app_hash'],
                'session_file' => Yii::getAlias('@runtime/session.madeline'),
            ]);
        }
        return $this->_mtProtoClient;
    }

    private function getTelegramUser(): ?UserRecord
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'tma ')) {
            return null;
        }

        $initData = substr($authHeader, 4);
        if (!$this->validateInitData($initData)) {
            return null;
        }

        parse_str($initData, $data);
        $userJson = json_decode($data['user'] ?? '{}', true);
        $telegramId = $userJson['id'] ?? null;

        if (!$telegramId) {
            return null;
        }

        return UserRecord::findOne(['telegram_id' => $telegramId, 'status' => 'active']);
    }

    private function validateInitData(string $initData): bool
    {
        parse_str($initData, $data);
        if (!isset($data['hash'])) {
            return false;
        }

        $hash = $data['hash'];
        unset($data['hash']);
        ksort($data);

        $dataCheckString = "";
        foreach ($data as $key => $value) {
            $dataCheckString .= "$key=$value\n";
        }
        $dataCheckString = rtrim($dataCheckString, "\n");

        $botToken = Yii::$app->params['telegram']['botToken'] ?? '';
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);
        $checkHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($checkHash, $hash);
    }

    /**
     * Поиск публичных каналов/групп в Telegram по ключевому слову
     */
    public function actionSearchChannels()
    {
        // Увеличиваем лимиты для тяжелой операции
        set_time_limit(60);
        ini_set('memory_limit', '512M');

        $user = $this->getTelegramUser();
        if (!$user) {
            Yii::$app->response->statusCode = 401;
            return ['error' => 'Unauthorized'];
        }

        $query = Yii::$app->request->get('query');
        if (!$query) {
            return ['error' => 'Search query required'];
        }

        Yii::debug("API: Starting MTProto search for query: {$query}");

        try {
            $client = $this->getMTProtoClient();
            Yii::debug('API: MTProtoClient instance obtained (or reused).');
            
            $channels = $client->searchPublicChannels($query);
            Yii::debug('API: Search completed successfully. Found ' . count($channels) . ' channels.');
            
            return $channels;
        } catch (\Exception $e) {
            Yii::error('API MTProto Error: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return ['error' => 'Search failed', 'details' => $e->getMessage()];
        }
    }

    public function actionGetProjects()
    {
        $user = $this->getTelegramUser();
        if (!$user) { Yii::$app->response->statusCode = 401; return ['error' => 'Unauthorized']; }

        $projects = ProjectRecord::find()->where(['user_id' => $user->id])->all();
        $result = [];
        foreach ($projects as $project) {
            $result[] = [
                'id' => $project->id,
                'name' => $project->name,
                'interval' => $project->digest_interval,
                'is_active' => (bool)$project->is_active,
            ];
        }
        return $result;
    }

    public function actionCreateProject()
    {
        $user = $this->getTelegramUser();
        if (!$user) return ['error' => 'Unauthorized'];

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $model = new ProjectRecord();
        $model->user_id = $user->id;
        $model->name = $data['name'] ?? '';
        $model->digest_interval = (int)($data['interval'] ?? 60);
        $model->is_active = true;

        if ($model->save()) return ['id' => $model->id, 'name' => $model->name];
        return ['error' => 'Validation failed', 'details' => $model->errors];
    }

    public function actionDeleteProject($id)
    {
        $user = $this->getTelegramUser();
        if (!$user) return ['error' => 'Unauthorized'];

        $project = ProjectRecord::findOne(['id' => $id, 'user_id' => $user->id]);
        if ($project && $project->delete()) return ['ok' => true];
        return ['error' => 'Failed to delete'];
    }

    public function actionGetProjectDetails($id)
    {
        $user = $this->getTelegramUser();
        if (!$user) return ['error' => 'Unauthorized'];

        $project = ProjectRecord::find()->where(['id' => $id, 'user_id' => $user->id])->with(['monitoredGroups', 'keywords'])->one();
        if (!$project) return ['error' => 'Not found'];

        $groups = array_map(fn($g) => ['id' => $g->id, 'username' => $g->telegram_username, 'name' => $g->name], $project->monitoredGroups);
        $keywords = array_map(fn($k) => ['id' => $k->id, 'word' => $k->word], $project->keywords);

        return [
            'id' => $project->id,
            'name' => $project->name,
            'interval' => $project->digest_interval,
            'groups' => $groups,
            'keywords' => $keywords,
        ];
    }

    public function actionAddKeyword($id)
    {
        $user = $this->getTelegramUser();
        if (!$user) return ['error' => 'Unauthorized'];

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $word = trim($data['word'] ?? '');
        
        if (empty($word)) return ['error' => 'Word required'];

        $analyzer = new MorphologyAnalyzer();
        $model = new KeywordRecord();
        $model->project_id = (int)$id;
        $model->word = $word;
        $model->setNormalizedFormsArray($analyzer->getNormalizedForms($word));

        if ($model->save()) {
            return ['id' => $model->id, 'word' => $model->word];
        }

        return ['error' => 'Save failed', 'details' => $model->errors];
    }

    public function actionDeleteKeyword($id)
    {
        $user = $this->getTelegramUser();
        if (!$user) return ['error' => 'Unauthorized'];

        $model = KeywordRecord::findOne($id);
        if ($model && $model->delete()) return ['ok' => true];
        return ['error' => 'Failed'];
    }

    public function actionAddGroup($id)
    {
        $user = $this->getTelegramUser();
        if (!$user) return ['error' => 'Unauthorized'];

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $username = trim($data['username'] ?? '');
        $title = trim($data['title'] ?? $username);
        
        if (empty($username)) return ['error' => 'Username required'];

        $model = new MonitoredGroupRecord();
        $model->project_id = (int)$id;
        $model->telegram_username = ltrim($username, '@');
        $model->name = $title ?: $model->telegram_username;

        if ($model->save()) {
            return [
                'id' => $model->id,
                'username' => $model->telegram_username,
                'name' => $model->name
            ];
        }
        return ['error' => 'Save failed', 'details' => $model->errors];
    }

    public function actionDeleteGroup($id)
    {
        $user = $this->getTelegramUser();
        if (!$user) return ['error' => 'Unauthorized'];

        $model = MonitoredGroupRecord::findOne($id);
        if ($model && $model->delete()) return ['ok' => true];
        return ['error' => 'Failed'];
    }
}
