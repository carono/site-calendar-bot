<?php

namespace app\commands;

use OpenAI;
use Yii;
use yii\console\Controller;

class GptController extends Controller
{
    public function actionTest()
    {

        $client = OpenAI::factory()
            ->withApiKey(Yii::$app->params['proxy-api']['token'])
            ->withBaseUri('api.proxyapi.ru/openai/v1')
            ->withHttpClient()
            ->make();


        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => 'тест'],
            ],
        ]);
        print_r($response);
//        return $response->choices[0]->message->content;
    }
}