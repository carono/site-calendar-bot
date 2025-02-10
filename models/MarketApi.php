<?php

/**
 * This class is generated using the package carono/codegen
 */

namespace app\models;

use app\exceptions\ValidationException;
use app\helpers\MarketHelper;
use app\helpers\RoundHelper;
use app\market\InfoDTO;
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

    /**
     * @param $external_id
     * @return InfoDTO
     */
    public function getOrderInfo($external_id)
    {
        $client = new $this->market->class_name;
        $client->setApi($this);
        return $client->getOrderInfo($external_id);
    }

    public function getBreakEvenPercent()
    {
        return $this->default_break_even_percent ?: ($this->user->default_break_even_percent ?: 0.03);
    }

    public function getCoinPrice($coin, $type)
    {
        $client = new $this->market->class_name;
        $client->setApi($this);
        return $client->getPrice($coin, $type);
    }


    public function roundRequestPrices(OrderRequest $request)
    {
        /**
         * @var \app\market\Market $client
         */


//        if ($request instanceof OrderLimitRequest) {
//            if (!$request->price) {
//                $request->price = $client->getPrice($request->coin, \app\market\Market::TYPE_SPOT);
//            }
//        }

//        $request->price = $request->price ? $this->roundPrice($request->price, $request->coin) : $request->price;

//        $request->take_profit1 = $request->take_profit1 ? $this->roundPrice($request->take_profit1, $request->coin) : $request->take_profit1;
//        $request->take_profit2 = $request->take_profit2 ? $this->roundPrice($request->take_profit2, $request->coin) : $request->take_profit2;
//        $request->take_profit3 = $request->take_profit3 ? $this->roundPrice($request->take_profit3, $request->coin) : $request->take_profit3;
//        $request->take_profit4 = $request->take_profit4 ? $this->roundPrice($request->take_profit4, $request->coin) : $request->take_profit4;
//
//        $request->price_max = $request->price_max ? $this->roundPrice($request->price_max, $request->coin) : $request->price_max;
//        $request->price_min = $request->price_min ? $this->roundPrice($request->price_min, $request->coin) : $request->price_min;

//        if (is_null($request->stop_loss)) {
//            $this->setDefaultStopLoss($request);
//        }


//        if (is_null($request->stop_loss)) {
//            $request->stop_loss = $request->stop_loss ? $this->roundPrice($request->stop_loss, $request->coin) : $request->stop_loss;
//        } else {
//            $request->stop_loss = null;
//        }

//        if ($request instanceof OrderLimitRequest && !$request->take_profit) {
//            $takeProfitLevel = $request->price * 0.05;
//            if ($request instanceof OrderLongRequest) {
//                $request->take_profit1 = $request->price + $takeProfitLevel;
//            } else {
//                $request->take_profit1 = $request->price - $takeProfitLevel;
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
        if (!$result = $client->makeOrder($request)) {
            $this->addError('id', current($request->getFirstErrors()));
        }

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
        if (!$price) {
            return null;
        }
        $settings = $this->getCoinSetting($symbol);
        $base = RoundHelper::getPrecisionBase($settings->order_precision);
        return RoundHelper::stripPrecision($price, $base);
    }

    public function getStopLoss($price, $side, $default = 0.03)
    {
        $percent = $this->default_stop_loss_percent ?: ($this->user->default_stop_loss_percent ?: $default);
        return $side == 'buy' ? MarketHelper::subPercent($price, $percent) : MarketHelper::addPercent($price, $percent);
    }

    public function getSum()
    {
        return 5;
    }

    public function getProfitStep($price, $side, $default = 0.03)
    {
        $percent = $this->default_break_even_percent ?: ($this->user->default_break_even_percent ?: $default);
        return $side == 'buy' ? MarketHelper::addPercent($price, $percent) : MarketHelper::subPercent($price, $percent);
    }
}
