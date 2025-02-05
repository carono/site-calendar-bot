<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\helpers\RoundHelper;
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

    public function getOrderInfo($external_id)
    {
        $client = new $this->market->class_name;
        $client->setApi($this);
        return $client->getOrderInfo($external_id);
    }

    public function prepareOrderRequest(OrderRequest $request)
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

        $request->price = $request->price ? $this->roundPrice($request->price, $request->coin) : $request->price;
        if (is_null($request->stop_loss)) {
            $request->stop_loss = $request->stop_loss ? $this->roundPrice($request->stop_loss, $request->coin) : $request->stop_loss;
        } else {
            $request->stop_loss = null;
        }
        $request->take_profit1 = $request->take_profit1 ? $this->roundPrice($request->take_profit1, $request->coin) : $request->take_profit1;
        $request->take_profit2 = $request->take_profit2 ? $this->roundPrice($request->take_profit2, $request->coin) : $request->take_profit2;
        $request->take_profit3 = $request->take_profit3 ? $this->roundPrice($request->take_profit3, $request->coin) : $request->take_profit3;
        $request->take_profit4 = $request->take_profit4 ? $this->roundPrice($request->take_profit4, $request->coin) : $request->take_profit4;

        $request->price_max = $request->price_max ? $this->roundPrice($request->price_max, $request->coin) : $request->price_max;
        $request->price_min = $request->price_min ? $this->roundPrice($request->price_min, $request->coin) : $request->price_min;

        if ($request instanceof OrderLimitRequest && !$request->stop_loss) {
            $stopLossLevel = $request->price * 0.1;
            if ($request instanceof OrderLongRequest) {
                $request->stop_loss = $request->price - $stopLossLevel;
            } else {
                $request->stop_loss = $request->price + $stopLossLevel;
            }
        }

//        if ($request instanceof OrderLimitRequest && !$request->take_profit) {
//            $takeProfitLevel = $request->price * 0.05;
//            if ($request instanceof OrderLongRequest) {
//                $request->take_profit = $request->price + $takeProfitLevel;
//            } else {
//                $request->take_profit = $request->price - $takeProfitLevel;
//            }
//        }


        return $request;
    }

    public function order(OrderRequest $request)
    {
        /**
         * @var \app\market\Market $client
         */
        $client = new $this->market->class_name;
        $client->setApi($this);
        $request = $this->prepareOrderRequest($request);
        $result = $client->makeOrder($request);
        unset($client);
        return $result;
    }

    public function getSettings($symbol, $type)
    {
        $client = new $this->market->class_name;
        $client->setApi($this);
        return $client->getSettings($symbol, $type);
    }

    /**
     * @param $symbol
     * @return PvMarketSetting|array|\yii\db\ActiveRecord|null
     * @throws \app\exceptions\ValidationException
     */
    public function getCoinSetting($symbol)
    {
        $coinModel = Coin::findOrCreateByCode($symbol);
        $pv = PvMarketSetting::find()->andWhere(['market_id' => $this->market_id, 'coin_id' => $coinModel->id])->cache(10)->one();
        if (!$pv) {
            $pv = $coinModel->updateSettings($this);
        }
        return $pv;
    }

    /**
     * @param $price
     * @param $symbol
     * @return string
     * @throws \app\exceptions\ValidationException
     */
    public function roundPrice($price, $symbol)
    {
        if (!$price){
            return null;
        }
        $settings = $this->getCoinSetting($symbol);
        $base = RoundHelper::getPrecisionBase($settings->order_precision);
        return RoundHelper::stripPrecision($price, $base);
    }
}
