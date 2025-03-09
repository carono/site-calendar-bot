<?php

namespace app\commands;

use app\clients\bybit\Client;
use app\components\Bot;
use app\helpers\AIHelper;
use app\helpers\MarketHelper;
use app\telegram\crypto_signal\determine\SignalDetermine;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class TestController extends Controller
{
    public function actionMarket()
    {
        $client = new Client();
        $client->token = Yii::$app->params['market']['bybit']['token'];
        $client->secret = Yii::$app->params['market']['bybit']['secret'];

//        $response = $client->getOrderbook('spot');
//        $response = $client->walletBalance('UNIFIED');
        $response = $client->instrumentsInfo('spot', 'PEPEUSDT');
        print_r($response);
    }

    public function actionIndex()
    {



        $file = file_get_contents(Yii::getAlias('@runtime/img.png'));
        $base64 = base64_encode($file);
        $request = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ["type" => "text","text" => "На этой картинке написаны имена игроков, их мощь и уровень, составь список, не комментируй, только данные: Имя, мощь, уровень"],
                        ["type" => "image_url","image_url" => ["url" => "data:image/png;base64,$base64"]]
                    ]
                ]
            ]
        ];
        $response = AIHelper::getClient()->chat()->create($request);
        print_r($response);
    }
}