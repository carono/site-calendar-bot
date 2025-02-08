<?php

namespace app\commands;

use app\clients\bybit\Client;
use app\telegram\crypto_signal\determine\SignalDetermine;
use Yii;
use yii\console\Controller;

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
        Yii::info('test', 'telegram');
    }
}