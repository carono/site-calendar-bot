<?php

namespace app\commands;

use app\helpers\MarketHelper;
use app\models\MarketApi;
use app\models\Order;
use app\telegram\crypto_signal\determine\SignalDetermine;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class MarketController extends Controller
{


    public function actionOrder()
    {
        /**
         * @var MarketApi $marketApi
         */
        $marketApi = MarketApi::find()->andWhere(['user_id' => 1, 'market_id' => 1])->one();

        $text = <<<HTML
Совершил сделку по покупке длинной позиции (LONG) по паре       ATOM/USDT  
        
Технические параметры:        
        
🟢  Покупка   ATOM/USDT      03.02.2025
        
💰 Диапазон входа     $4,20000  -  $4,44000
        
🎯 Цель 1 -     $4,80000    
        
🎯 Цель 2 -     $5,40000    
        
        
⛔️ Стоп за уровень   -      
        
Актив у исторического минимума, берем без стопа        
        
        
Рекомендация: После движения цены порядка 2-3 % в нашу сторону от точки входа, не дожидаться тейков, сразу переводить стоп в БУ, так же можно его пододвигать в след за ценой держа его в районе 2-3% от текущей стоимости.        
        
👍🏼- вхожу в сделку        
👀- не успел войти        
🤷- пропускаю        
🧑‍💻- зайду в следующую        
Все реакции в канале ограничены именно по этому принципу.        
Всем удачи и большого профита. 🥰
HTML;

        $request = MarketHelper::textToMarketRequest($text);


//        $request = new OrderLongRequest();
//        $request->coin = 'XRPUSDT';
//        $request->sum = 5;


//        $marketApi->order($request);
    }


    public function actionCheck()
    {
        /**
         * @var Order $order
         */
        foreach (Order::find()->andWhere(['not', ['executed_at' => null]])->andWhere(['not', ['external_id' => null]])->each() as $order) {
            $api = $order->marketApi;
            try {
                $info = $api->getOrderInfo(trim($order->external_id));
                $price = $api->getCoinPrice($order->coin->code, $order->type);
                $order->updateAttributes([
                    'status' => $info->orderStatus,
                    'last_updated_price' => $price,
                    'price_fact' => $info->price
                ]);
            } catch (Exception $e) {
                Console::output('Order ' . $order->external_id . ': ' . $e->getMessage());
            }
        }
    }
}