<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\market\order\OrderLimitRequest;
use app\market\order\OrderLongRequest;
use app\market\order\OrderRequest;

/**
 * This is the model class for table "market_api".
 */
class MarketApi extends base\MarketApi
{
    /**
     * @return \app\market\WalletDTO[]
     */
    public function getWallet()
    {
        /**
         * @var $client \app\market\Market
         */
        $client = new $this->market->class_name;
        $client->setApi($this);
        $result = $client->getWallet();
        unset($client);
        return $result;
    }

    public function order(OrderRequest $request)
    {
        /**
         * @var \app\market\Market $client
         */
        $client = new $this->market->class_name;
        $client->setApi($this);

        if ($request instanceof OrderLimitRequest) {
            if (!$request->price) {
                $request->price = $client->getPrice($request->coin, \app\market\Market::TYPE_SPOT);
            }
        }

        if ($request instanceof OrderLimitRequest && !$request->stop_loss) {
            $stopLossLevel = $request->price * 0.03;
            if ($request instanceof OrderLongRequest) {
                $request->stop_loss = $request->price - $stopLossLevel;
            } else {
                $request->stop_loss = $request->price + $stopLossLevel;
            }
        }

        if ($request instanceof OrderLimitRequest && !$request->take_profit) {
            $takeProfitLevel = $request->price * 0.05;
            if ($request instanceof OrderLongRequest) {
                $request->take_profit = $request->price + $takeProfitLevel;
            } else {
                $request->take_profit = $request->price - $takeProfitLevel;
            }
        }

        var_dump($request);
        exit;

//        $result = $client->makeOrder($request);
//        unset($client);
        return $result;
    }

    public function getSettings($symbol, $type)
    {
        $client = new $this->market->class_name;
        $client->setApi($this);
        return $client->getSettings($symbol, $type);
    }
}
