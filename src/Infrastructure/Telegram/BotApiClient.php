<?php

namespace app\Infrastructure\Telegram;

class BotApiClient
{
    private string $botToken;
    private string $apiUrl = 'https://api.telegram.org/bot';

    public function __construct(string $botToken)
    {
        $this->botToken = $botToken;
    }

    public function sendMessage(int $chatId, string $text, ?array $replyMarkup = null): array
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->request('sendMessage', $params);
    }

    public function setWebhook(string $url): array
    {
        return $this->request('setWebhook', ['url' => $url]);
    }

    public function deleteWebhook(): array
    {
        return $this->request('deleteWebhook');
    }

    public function getMe(): array
    {
        return $this->request('getMe');
    }

    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null): array
    {
        $params = ['callback_query_id' => $callbackQueryId];

        if ($text !== null) {
            $params['text'] = $text;
        }

        return $this->request('answerCallbackQuery', $params);
    }

    private function request(string $method, array $params = []): array
    {
        $url = $this->apiUrl . $this->botToken . '/' . $method;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException("Telegram API request failed with code $httpCode: $response");
        }

        $result = json_decode($response, true);

        if (!$result['ok']) {
            throw new \RuntimeException('Telegram API error: ' . ($result['description'] ?? 'Unknown error'));
        }

        return $result['result'];
    }
}
