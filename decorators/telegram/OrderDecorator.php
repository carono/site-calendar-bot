<?php

namespace app\decorators\telegram;

use app\helpers\MarketHelper;
use app\market\OrderInfoDTO;
use app\models\MarketApi;

class OrderDecorator
{
    public static function getSideEmoji($side)
    {
        return $side == 'buy' ? '🟢' : '🔴';
    }

    public static function shortOrderInfo(OrderInfoDTO $dto, MarketApi $api = null)
    {

        $profit = null;
        if ($api) {
            $currentPrice = $api->getCoinPrice($dto->symbol, 'spot');
            // $message[] = $currentPrice;
            $profit = MarketHelper::getRangePercent($dto->basePrice, $currentPrice, true);
        }


        $message = [];
        $message[] = $profit <= 0 ? '🔴' : '🟢';
        $message[] = $dto->symbol;


//        $message[] = ': ' . ($dto->takeProfit ?: '-') . ' / ' . ($dto->stopLoss ?: '-') . ' / ' . $profit;
//        $message[] = ': ' . MarketHelper::getRangePercent($dto->price, $dto->takeProfit, true) . ' / ' . MarketHelper::getRangePercent($dto->stopLoss, $dto->price, true) . ' / ' . $profit;
        $message[] = ': ' . $profit;


        return implode(' ', $message);
    }
}