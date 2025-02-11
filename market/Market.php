<?php

namespace app\market;

use app\market\order\OrderLongRequest;
use app\models\MarketApi;

abstract class Market
{
    const TYPE_SPOT = 'spot';
    const TYPE_MARKET = 'market';

    const METHOD_BUY = 'buy';

    const METHOD_SELL = 'sell';
    /**
     * @var MarketApi
     */
    protected $_api;

    public function setApi(MarketApi $api): void
    {
        $this->_api = $api;
    }

    public function getApi(): MarketApi
    {
        return $this->_api;
    }

    /**
     * @return WalletDTO[]
     */
    abstract public function getWallet();

    abstract public function makeOrder(OrderLongRequest $request);

    abstract public function getPrice(string $coin, string $type = self::TYPE_SPOT, string $method = self::METHOD_BUY);

    abstract public function getSettings($coin, $type = self::TYPE_SPOT);

    abstract public function getOrderInfo($external_id);

    abstract public function getOpenOrders();
}