<?php

namespace app\decorators\telegram;

use app\helpers\MarketHelper;
use app\market\OrderInfoDTO;
use app\models\MarketApi;
use Yii;

class OrderDecorator
{
    public static function getSideEmoji($side)
    {
        return $side == 'buy' ? '🟢' : '🔴';
    }

    public static function shortOrderInfo(OrderInfoDTO $dto, MarketApi $api = null)
    {

        $currentPrice = $api->getCoinPrice($dto->symbol, 'spot');


        $message = [];

//        $message[] = $dto->side == 'Buy' ? 'BUY' : 'SELL';
        if ($dto->price) {
            if ($dto->side == 'Buy') {
                $message[] = 'Покупаем';
                $message[] = $dto->symbol;
                $message[] = 'Цель ' . $dto->price;
                $message[] = '(' . Yii::$app->formatter->asPercent(1 - MarketHelper::getRangePercent($dto->price, $currentPrice), 2) . ')';
            } else {
                $message[] = 'Продаем';
                $message[] = $dto->symbol;
                $message[] = 'Цель ' . $dto->price;
                $message[] = '(' . Yii::$app->formatter->asPercent(1 - MarketHelper::getRangePercent($currentPrice, $dto->price), 2) . ')';
            }
        } else {
            $profit = MarketHelper::getRangePercent($dto->basePrice, $currentPrice, false);
            if ($dto->side == 'Buy') {
                $message[] = 'LONG';
                $message[] = $dto->symbol;
            } else {
                $message[] = $profit == 0 ? '⭕️' : ($profit <= 0 ? '🔽' : '🔼');
                $message[] = 'SHORT';
                $message[] = $dto->symbol;
                $message[] = 'Цель ' . MarketHelper::getRangePercent($dto->basePrice, $dto->triggerPrice, true);
                $message[] = '(' . Yii::$app->formatter->asPercent($profit, 2) . ')';
            }
        }

//        $message[] = ': ' . ($dto->takeProfit ?: '-') . ' / ' . ($dto->stopLoss ?: '-') . ' / ' . $profit;
//        $message[] = ': ' . MarketHelper::getRangePercent($dto->price, $dto->takeProfit, true) . ' / ' . MarketHelper::getRangePercent($dto->stopLoss, $dto->price, true) . ' / ' . $profit;
//        $message[] = ': ' . Yii::$app->formatter->asPercent($profit, 2);


        return implode(' ', $message);
    }
}