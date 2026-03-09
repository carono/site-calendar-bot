<?php

namespace app\jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Yii;
use yii\queue\JobInterface;

class AskModelJob implements JobInterface
{

    public  $modelId = '';

    public  $system = '';

    public  $prompt = '';

    public array $imagesBase64 = [];

    public function execute($queue): void
    {
        $messages = [];

        if ($this->system) {
            $messages[] = ['role' => 'system', 'content' => $this->system];
        }

        foreach ($this->imagesBase64 as $base64) {
            $messages[] = [
                'role' => 'user',
                'content' => [
                    ['type' => 'image_url', 'image_url' => ['url' => "data:image/png;base64,$base64"]],
                ],
            ];
        }

        if ($this->prompt) {
            $messages[] = ['role' => 'user', 'content' => $this->prompt];
        }


        $answer = $messages ? $this->callApi($messages) : 'Ошибка: запрос пустой (не задан prompt)';

        $answers = Yii::$app->cache->get('gpt-form-answers') ?: [];
        $answers[$this->modelId] = $answer;
        Yii::$app->cache->set('gpt-form-answers', $answers);

        $pending = Yii::$app->cache->get('gpt-form-pending') ?: [];
        Yii::$app->cache->set('gpt-form-pending', array_values(array_diff($pending, [$this->modelId])));
    }

    private function callApi(array $messages): string
    {
        $token = Yii::$app->params['proxy-api']['token'];
        $baseUri = 'https://routerai.ru/api/v1/';

        $client = new Client(['timeout' => 120]);

        try {
            $response = $client->post($baseUri.'chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->modelId,
                    'messages' => $messages,
                ],
//                'proxy' => 'localhost:8888',
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return $data['choices'][0]['message']['content'] ?? '';
        } catch (TransferException $e) {
            // Guzzle HTTP-level error: разбираем тело ответа чтобы показать реальную ошибку API
            $body = '';
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $body = (string)$e->getResponse()->getBody();
            }

            $decoded = $body ? json_decode($body, true) : null;
            $error = $decoded['error'] ?? null;

            if (is_array($error)) {
                $msg = $error['message'] ?? json_encode($error);
            } elseif (is_string($error) && $error !== '') {
                $msg = $error;
            } else {
                $msg = $body ?: $e->getMessage();
            }

            Yii::error("[{$this->modelId}] API error: $msg");

            return "Ошибка API: $msg";
        } catch (\Throwable $e) {
            Yii::error($e);

            return 'Ошибка: '.$e->getMessage();
        }
    }

}
