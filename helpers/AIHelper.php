<?php

namespace app\helpers;

use app\ai\System;
use app\models\gpt\DetermineDTO;
use GuzzleHttp\Client;
use OpenAI;
use Yii;

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
            ->withBaseUri('api.proxyapi.ru/openai/v1')
            ->withHttpClient(new Client())
            ->make();
    }

    /**
     * @return static
     */
    public static function start()
    {
        return new static();
    }

    protected function ask(string $question)
    {
        $request = [
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge($this->systemCommands, [
                [
                    'role' => 'user',
                    'content' => $question
                ]
            ])
        ];
        file_put_contents(Yii::getAlias('@runtime/cache/request.json'), json_encode($request['messages'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $response = static::getClient()->chat()->create($request);
        file_put_contents(Yii::getAlias('@runtime/cache/response.json'), $response->choices[0]->message->content);
        return $response;
    }

    public function determine(string $question)
    {
        $response = $this->ask($question);
        return new DetermineDTO(json_decode($response->choices[0]->message->content, true));
    }
}