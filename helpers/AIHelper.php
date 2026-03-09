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
    private $systemCommands = [];

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
//            ->withHttpClient(new Client(['proxy' => 'http://qBe5JW:9u047m@191.102.147.228:8000', 'verify' => false]))
//            ->withHttpClient(new Client(['proxy' => '192.168.1.254:8888', 'verify' => false]))
            ->make();
    }

    /**
     * @return static
     */
    public static function start()
    {
        return new static();
    }

    public function ask(string $question, $messages = [])
    {
        $request = [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($messages, [
                [
                    'role' => 'user',
                    'content' => $question
                ]
            ])
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
            'content' => $command->prompt
        ];
        return $this;
    }
}