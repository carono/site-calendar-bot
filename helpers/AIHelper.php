<?php

namespace app\helpers;

use app\ai\Command;
use app\ai\System;
use app\models\gpt\DetermineDTO;
use GuzzleHttp\Client;
use OpenAI;
use Yii;
use yii\helpers\ArrayHelper;

class AIHelper
{

    const MODELS = [
        'openai/gpt-5.4' => 'GPT-5.4',
        'anthropic/claude-sonnet-4.6' => 'Claude Sonnet 4.6',
        'x-ai/grok-4' => 'Grok 4',
    ];

    const DEFAULT_MODEL = 'openai/gpt-5.4';

    private $systemCommands = [];

    private string $model = self::DEFAULT_MODEL;

    public function withModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function addSystem(System $system)
    {
        if ($message = $system->getMessage()) {
            if (isset($message['role'])) {
                $this->systemCommands[] = $message;
            } else {
                $this->systemCommands = array_merge($this->systemCommands, $message);
            }
        }

        return $this;
    }

    public static function getClient()
    {
        return OpenAI::factory()
            ->withApiKey(Yii::$app->params['proxy-api']['token'])
            ->withBaseUri('routerai.ru/api/v1')
            ->make();
    }

    /**
     * @return static
     */
    public static function start(?string $model = null)
    {
        $instance = new static();
        if ($model !== null) {
            $instance->withModel($model);
        }

        return $instance;
    }

    public function ask(string $question, $messages = [])
    {
        $request = [
            'model' => $this->model,
            'messages' => array_merge($this->systemCommands, $messages, [
                [
                    'role' => 'user',
                    'content' => $question,
                ],
            ]),
        ];

        return static::getClient()->chat()->create($request);
    }

    public function determine(string $question)
    {
        return $this->ask($question, DetermineDTO::class);
    }

    public function addCommand(Command $command)
    {
        $this->systemCommands[] = [
            'role' => 'system',
            'content' => $command->prompt,
        ];

        return $this;
    }

}