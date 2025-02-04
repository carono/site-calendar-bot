<?php

namespace app\helpers;

use app\exceptions\market\ForbiddenOrderException;
use app\market\order\OrderLongRequest;
use app\telegram\crypto_signal\determine\OrderRequest;
use Yii;

class MarketHelper
{
    public static function textToMarketRequest($text)
    {
        $determine = new OrderRequest();
        $key = ['gpt', 'determine', md5($text)];
        if (!$response = Yii::$app->cache->get($key)) {
            $response = $determine->process($text);
            Yii::$app->cache->set($key, $response, 360);
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

        if (isset($response['token'])) {
            $request->coin = $response['token'];
        }

        if (isset($response['stop'])) {
            $request->stop_loss = $response['stop'];
        }

        return $request;
    }
}