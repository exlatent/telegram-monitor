<?php

namespace app\Infrastructure\Telegram;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Logger;

use Yii;

class MTProtoClient
{
    private ?API $client = null;
    private string $sessionFile;
    private bool $isStarted = false;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->sessionFile = $config['session_file'] ?? 'session.madeline';
    }

    private function initClient(): void
    {
        if ($this->client !== null) {
            return;
        }

        Yii::debug('Initializing MadelineProto API with session: ' . $this->sessionFile);

        $settings = new Settings();
        $settings->getAppInfo()
            ->setApiId((int) $this->config['app_id'])
            ->setApiHash($this->config['app_hash']);

        $settings->getLogger()
            ->setType(Logger::ECHO_LOGGER)
            ->setLevel(Logger::LEVEL_FATAL); // минимум шума

        try {
            $this->client = new API($this->sessionFile, $settings);
            Yii::debug('MadelineProto API instance created successfully.');
        } catch (\Throwable $e) {
            Yii::error('Failed to create MadelineProto instance: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function start(): void
    {
        if (!$this->isStarted) {
            $this->initClient();
            try {
                $this->client->start();
                $this->isStarted = true;
            } catch (\Exception $e) {
                Yii::error('MadelineProto start error: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
                throw $e;
            }
        }
    }

    /**
     * Поиск публичных каналов/групп по ключевому слову
     */
    public function searchPublicChannels(string $query): array
    {
        $this->start();

        try {
            $results = $this->client->contacts->search(['q' => $query, 'limit' => 20]);
            $channels = [];

            if (isset($results['chats'])) {
                foreach ($results['chats'] as $chat) {
                    if (isset($chat['username'])) {
                        $channels[] = [
                            'id'       => $chat['id'],
                            'title'    => $chat['title'] ?? $chat['username'],
                            'username' => $chat['username'],
                            'type'     => $chat['_'] ?? 'unknown',
                        ];
                    }
                }
            }
            return $channels;
        } catch (\Exception $e) {
            Yii::error('MadelineProto searchPublicChannels error: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function getPeerInfo(string $username)
    {
        $this->start();
        return $this->client->getPwrChat($username);
    }

    public function getHistory($peer, int $offsetId = 0, int $limit = 100): array
    {
        $this->start();
        $messages = $this->client->messages->getHistory([
            'peer'        => $peer,
            'offset_id'   => $offsetId,
            'offset_date' => 0,
            'add_offset'  => 0,
            'limit'       => $limit,
            'max_id'      => 0,
            'min_id'      => 0,
            'hash'        => 0,
        ]);
        return $messages['messages'] ?? [];
    }

    public function getNewMessages($peer, int $lastMessageId = 0, int $limit = 100): array
    {
        $allMessages = $this->getHistory($peer, 0, $limit);
        $newMessages = [];
        foreach ($allMessages as $message) {
            if (isset($message['id']) && $message['id'] > $lastMessageId) {
                $newMessages[] = $message;
            }
        }
        return $newMessages;
    }

    public function getMessageLink(string $username, int $messageId): string
    {
        $username = ltrim($username, '@');
        return "https://t.me/{$username}/{$messageId}";
    }

    public function extractMessageText(array $message): ?string
    {
        return $message['message'] ?? null;
    }

    public function extractMessageId(array|null $message): int
    {
        return $message['id'] ?? 0;
    }

    public function extractMessageDate(array|null $message): int
    {
        return $message['date'] ?? time();
    }
}
