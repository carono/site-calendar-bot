<?php

namespace app\models\gpt;

use app\helpers\AIHelper;
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
        $client = AIHelper::getClient();

        foreach ((array)$this->models as $modelId) {
            if (!isset(AIHelper::MODELS[$modelId])) {
                continue;
            }
            $response = $client->chat()->create(['model' => $modelId, 'messages' => $messages]);
            $this->answers[$modelId] = $response->choices[0]->message->content ?? '';
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

        $response = AIHelper::getClient()->chat()->create([
            'model' => $this->consensusModel,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Ты эксперт-аналитик, который оценивает согласованность ответов нескольких AI-моделей на экзаменационные и тестовые вопросы.',
                ],
                ['role' => 'user', 'content' => $content],
            ],
        ]);

        $this->consensusResult = $response->choices[0]->message->content ?? '';
        Yii::$app->cache->set('gpt-form-consensus-model', $this->consensusModel);
    }

}
