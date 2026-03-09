<?php

namespace app\models\gpt;

use app\helpers\AIHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class GptForm extends Model
{

    public $image;

    public $prompt;

    public $system;

    public $models = [];

    public $answers = [];

    public $consensusModel;

    public $consensusResult;

    public function rules()
    {
        return [
            [['image', 'prompt', 'system', 'models', 'consensusModel'], 'safe'],
            [['image'], 'file'],
        ];
    }

    public function init()
    {
        $this->system = Yii::$app->cache->get('gpt-form-system');
        $this->prompt = Yii::$app->cache->get('gpt-form-prompt');
        $this->models = Yii::$app->cache->get('gpt-form-models') ?: array_keys(AIHelper::MODELS);
        $this->consensusModel = Yii::$app->cache->get('gpt-form-consensus-model') ?: AIHelper::DEFAULT_MODEL;
        $this->answers        = Yii::$app->cache->get('gpt-form-answers') ?: [];
    }

    private function buildMessages(): array
    {
        $messages = [];

        if ($this->system) {
            $messages[] = ['role' => 'system', 'content' => $this->system];
        }

        if ($images = UploadedFile::getInstances($this, 'image')) {
            foreach ($images as $image) {
                $base64 = base64_encode(file_get_contents($image->tempName));
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'image_url', 'image_url' => ['url' => "data:image/png;base64,$base64"]],
                    ],
                ];
            }
        }

        if ($this->prompt) {
            $messages[] = ['role' => 'user', 'content' => $this->prompt];
        }

        return $messages;
    }

    public function askAll()
    {
        $messages = $this->buildMessages();

        if ($this->system) {
            Yii::$app->cache->set('gpt-form-system', $this->system);
        } else {
            Yii::$app->cache->delete('gpt-form-system');
        }
        if ($this->prompt) {
            Yii::$app->cache->set('gpt-form-prompt', $this->prompt);
        } else {
            Yii::$app->cache->delete('gpt-form-prompt');
        }
        Yii::$app->cache->set('gpt-form-models', (array)$this->models);

        $this->answers = [];

        foreach ((array)$this->models as $modelId) {
            if (!isset(AIHelper::MODELS[$modelId])) {
                continue;
            }
            $this->answers[$modelId] = $this->callApi($modelId, $messages);
        }

        Yii::$app->cache->set('gpt-form-answers', $this->answers);
    }

    public function checkConsensus()
    {
        $this->answers = Yii::$app->cache->get('gpt-form-answers') ?: [];
        $question = Yii::$app->cache->get('gpt-form-prompt') ?: '';

        if (!$this->answers) {
            $this->consensusResult = 'Нет сохранённых ответов для анализа. Сначала задайте вопрос всем моделям.';

            return;
        }

        $content = "Вопрос:\n{$question}\n\n";
        foreach ($this->answers as $modelId => $answer) {
            $modelName = AIHelper::MODELS[$modelId] ?? $modelId;
            $content .= "--- Ответ от {$modelName} ---\n{$answer}\n\n";
        }
        $content .= "Проанализируй ответы нескольких AI-моделей на вопрос из теста/экзамена. "
            ."Определи: (1) пришли ли модели к единому ответу (консенсус есть / нет), "
            ."(2) если есть расхождения — укажи, какая версия более убедительна и почему, "
            ."(3) дай итоговый рекомендуемый ответ и оцени степень доверия: высокая / средняя / низкая.";

        $this->consensusResult = $this->callApi($this->consensusModel, [
            [
                'role'    => 'system',
                'content' => 'Ты эксперт-аналитик, который оценивает согласованность ответов нескольких AI-моделей на экзаменационные и тестовые вопросы.',
            ],
            ['role' => 'user', 'content' => $content],
        ]);
        Yii::$app->cache->set('gpt-form-consensus-model', $this->consensusModel);
    }

    private function callApi(string $model, array $messages): string
    {
        $client = new Client(['timeout' => 120]);
        try {
            $response = $client->post('https://routerai.ru/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . Yii::$app->params['proxy-api']['token'],
                    'Content-Type'  => 'application/json',
                ],
                'json' => ['model' => $model, 'messages' => $messages],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['choices'][0]['message']['content'] ?? '';
        } catch (TransferException $e) {
            $body    = method_exists($e, 'getResponse') && $e->getResponse()
                ? (string)$e->getResponse()->getBody()
                : '';
            $decoded = $body ? json_decode($body, true) : null;
            $error   = $decoded['error'] ?? null;
            if (is_array($error)) {
                return 'Ошибка API: ' . ($error['message'] ?? json_encode($error));
            }
            return 'Ошибка API: ' . ($error ?: $body ?: $e->getMessage());
        } catch (\Throwable $e) {
            return 'Ошибка: ' . $e->getMessage();
        }
    }

}
