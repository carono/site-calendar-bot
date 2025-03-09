<?php

namespace app\commands;

use app\helpers\MarketHelper;
use app\models\MarketApi;
use yii\console\Controller;

class OrderController extends Controller
{
    public function actionCheck()
    {

        foreach (MarketApi::find()->each() as $marketApi) {
            foreach ($marketApi->getOpenOrders() as $order) {
                $currentPrice = $marketApi->getCoinPrice($order->symbol, 'spot');
                $buyPrice = $order->price;
                $diff = MarketHelper::getRangePercent($buyPrice, $currentPrice);
                if ($diff >= 0.01) {
                    if ($marketApi->cancel($order->id)) {

                    }
                }
                var_dump($diff);
                var_dump($currentPrice);
                var_dump($buyPrice);
                exit;
//                $message[] = OrderDecorator::shortOrderInfo($order, $marketApi);
            }
        }
    }
}