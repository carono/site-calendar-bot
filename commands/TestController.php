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
        $message = <<<HTML
Совершил сделку по покупке длинной позиции (LONG) по паре       COMP/USDT  
        
Технические параметры:        
        
🟢  Покупка   COMP/USDT      30.01.2025
        
💰 Диапазон входа     $69,00000  -  $70,00000
        
🎯 Цель 1 -     $72,00000    
        
🎯 Цель 2 -     $75,00000    
        
        
⛔️ Стоп за уровень   -  $68,60000    
        
Берем в продолжение роста        
        
        
Рекомендация: После движения цены порядка 2-3 % в нашу сторону от точки входа, не дожидаться тейков, сразу переводить стоп в БУ, так же можно его пододвигать в след за ценой держа его в районе 2-3% от текущей стоимости.        
        
👍🏼- вхожу в сделку        
👀- не успел войти        
🤷- пропускаю        
🧑‍💻- зайду в следующую        
Все реакции в канале ограничены именно по этому принципу.        
Всем удачи и большого профита. 🥰
HTML;

        $message2 = <<<HTML
#слухи 
Наш источник сообщает, что после того, как россияне взломали ДИЮ, они получили частичный доступ к данным по за бронированным от мобилизации украинцам, где была указана вся информация, где и кем работают.
Так вот эти данные помогли россиянам выявить децентрализованные предприятия по производству и сбору fpv дронов. После этого туда прилетели БПЛА Герань («шахеды»). Украинские производители понесли ущерб на миллионы долларов и потеряли производственные линии.

Данную информацию всячески замалчивают.
HTML;


        $determine = new SignalDetermine();


//       $result =  $determine->check($message);
//var_dump($result);
        $result = $determine->process($message);
        print_r($result);
//        file_put_contents(Yii::getAlias('@app/runtime/cache/result.json'), $result);
    }
}