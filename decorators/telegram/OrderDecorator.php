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
        $message = [];
        $message[] = static::getSideEmoji($dto->side);
        $message[] = $dto->symbol;
        $profit = null;
        if ($api) {
            $currentPrice = $api->getCoinPrice($dto->symbol, 'spot');
            $message[] = $currentPrice;
            $profit = MarketHelper::getRangePercent($dto->basePrice, $currentPrice, true);
        }

        $message[] = ': ' . ($dto->takeProfit ?: '-') . ' / ' . ($dto->stopLoss ?: '-') . ' / ' . $profit;
        return implode(' ', $message);
    }
}