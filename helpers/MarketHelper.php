<?php

namespace app\helpers;

use app\exceptions\market\ForbiddenOrderException;
use app\market\order\OrderLongRequest;
use app\telegram\crypto_signal\determine\SignalDetermine;
use Yii;
use yii\helpers\ArrayHelper;

class MarketHelper
{
    public static function textToMarketRequest($text)
    {
        $determine = new SignalDetermine();
        $key = ['gpt', 'determine', md5($text)];
        if (!$response = Yii::$app->cache->get($key)) {
            $response = $determine->process($text);
//            Yii::$app->cache->set($key, $response, 360);
        }
        if ($response['type'] == "LONG") {
            $request = new OrderLongRequest();
        } else {
            throw new ForbiddenOrderException();
        }

        if (isset($response['buy'])) {
            $request->price_min = min((array)$response['buy']);
            $request->price_max = max((array)$response['buy']);
        }

        if (isset($response['target'])) {
            $target = (array)$response['target'];
            if ($request instanceof OrderLongRequest) {
                sort($target);
            } else {
                asort($target);
            }
            $request->take_profit1 = ArrayHelper::getValue($target, 0);
            $request->take_profit2 = ArrayHelper::getValue($target, 1);
            $request->take_profit3 = ArrayHelper::getValue($target, 2);
            $request->take_profit4 = ArrayHelper::getValue($target, 3);
        }

        if (isset($response['token'])) {
            $request->coin = str_replace('/', '', $response['token']);
        }

        if (isset($response['stop'])) {
            $request->stop_loss = $response['stop'];
        }

        return $request;
    }

    public static function addPercent($price, $percent)
    {
        $sub = $price * $percent;
        return $price + $sub;
    }

    public static function subPercent($price, $percent)
    {
        $sub = $price * $percent;
        return $price - $sub;
    }

    public static function getRangePercent($int1, $int2, $format = false)
    {
        $diff = $int2 - $int1;
        if ($int1 == 0) {
            return '0';
        }
        $result = RoundHelper::stripPrecision($diff / $int1, 4);
        return $format ? Yii::$app->formatter->asPercent($result, 3) : $result;
    }
}