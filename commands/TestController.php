<?php

namespace app\commands;

use app\clients\bybit\Client;
use app\components\Bot;
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
        $x = MarketHelper::getRangePercent('0.6519', '0.682');
        Console::output(Yii::$app->formatter->asPercent($x, 2));
    }
}