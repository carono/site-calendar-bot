<?php

namespace app\market;

use app\clients\bybit\Client;
use app\helpers\RoundHelper;
use app\market\order\OrderLongRequest;
use app\market\order\OrderRequest;
use app\models\PvMarketSetting;
use Yii;

class BybitMarket extends Market
{
    protected function getClient()
    {
        $client = new Client();
        $client->token = $this->getApi()->token;
        $client->secret = $this->getApi()->secret;
        return $client;
    }

    public function getWallet()
    {
        $client = $this->getClient();
        $response = $client->walletBalance();
        $result = [];
        foreach ($response->result->list as $item) {
            foreach ($item->coin as $coin) {
                $dto = new WalletDTO();
                $dto->coin = $coin->coin;
                $dto->balance = $coin->walletBalance;
                $result[] = $dto;
            }
        }
        return $result;
    }

    public function getSettings($coin, $type = self::TYPE_SPOT)
    {
        $response = $this->getClient()->instrumentsInfo($type, $coin);
        $item = $response->result->list[0];
        return [
            'base_precision' => $item->lotSizeFilter->basePrecision,
            'order_precision' => $item->priceFilter->tickSize,
            'min_quantity' => $item->lotSizeFilter->minOrderQty,
            'min_amount' => $item->lotSizeFilter->minOrderAmt,
        ];
    }

    /**
     * @param $symbol
     * @return PvMarketSetting|array|\yii\db\ActiveRecord|null
     * @throws \app\exceptions\ValidationException
     */

    /**
     * @param OrderRequest $request
     * @return int|void
     * @throws \app\exceptions\ValidationException
     */
    public function makeOrder(OrderRequest $request)
    {
        $settings = $this->getApi()->getCoinSetting($request->coin);
        $client = $this->getClient();
        $params = [
            'price' => (string)($price = $this->getApi()->roundPrice($request->price, $request->coin)),
            'stopLoss' => (string)$this->getApi()->roundPrice($request->stop_loss, $request->coin),
            'takeProfit' => (string)$this->getApi()->roundPrice($request->take_profit1, $request->coin),
            "tpLimitPrice" => (string)$this->getApi()->roundPrice($request->take_profit1, $request->coin),
            "slLimitPrice" => (string)$this->getApi()->roundPrice($request->stop_loss, $request->coin),
            'timeInForce' => 'PostOnly',
            "tpOrderType" => "Limit",
            "slOrderType" => "Limit"
        ];
        $base = RoundHelper::getPrecisionBase($this->getApi()->getCoinSetting($request->coin)->base_precision);

        $qt = round($request->sum / $price, $base);
        while ($settings->min_amount > ($qt * $price)) {
            $qt += round($settings['base_precision'], $base);
        }

        $side = $request instanceof OrderLongRequest ? 'Buy' : 'Sell';
        $type = 'Limit';
        $response = $client->order('spot', $request->coin, $side, $type, (string)$qt, array_filter($params));
        if (isset($response->result->orderId)) {
            return (int)$response->result->orderId;
        }
    }

    public function getPrice($coin, $type = self::TYPE_SPOT, $method = self::METHOD_BUY)
    {
        $client = $this->getClient();
        $response = $client->getOrderbook($type, $coin);
        $result = $response->result;
        if ($method == self::METHOD_BUY) {
            return $result->a[0][0];
        }
        return $result->b[0][0];
    }

    public function getOrderInfo($external_id)
    {
        return $this->getClient()->getOrderInfo('spot', ['orderId' => $external_id]);
    }
}