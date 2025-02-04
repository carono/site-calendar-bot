<?php

namespace app\market;

use app\clients\bybit\Client;
use app\helpers\RoundHelper;
use app\market\order\OrderLongRequest;
use app\market\order\OrderRequest;
use app\models\Coin;
use app\models\PvMarketSetting;

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
    protected function getCoinSetting($symbol)
    {
        $coinModel = Coin::findOrCreateByCode($symbol);
        $pv = PvMarketSetting::find()->andWhere(['market_id' => $this->getApi()->market_id, 'coin_id' => $coinModel->id])->cache(10)->one();
        if (!$pv) {
            $pv = $coinModel->updateSettings($this->getApi());
        }
        return $pv;
    }

    protected function roundPrice($price, $symbol)
    {
        $pv = $this->getCoinSetting($symbol);
        $base = RoundHelper::getPrecisionBase($pv->order_precision);
        return RoundHelper::stripPrecision($price, $base);
    }

    public function makeOrder(OrderRequest $request)
    {
        $settings = $this->getCoinSetting($request->coin);
        $client = $this->getClient();
        $params = [
            'price' => (string)($price = $this->roundPrice($request->price, $request->coin)),
            'stopLoss' => (string)$this->roundPrice($request->stop_loss, $request->coin),
            'takeProfit' => (string)$this->roundPrice($request->take_profit, $request->coin),
            "tpLimitPrice" => (string)$this->roundPrice($request->take_profit, $request->coin),
            "slLimitPrice" => (string)$this->roundPrice($request->stop_loss, $request->coin),
            'timeInForce' => 'PostOnly',
            "tpOrderType" => "Limit",
            "slOrderType" => "Limit"
        ];
        $base = RoundHelper::getPrecisionBase($this->getCoinSetting($request->coin)->base_precision);

        $qt = round($request->sum / $price, $base);
        while ($settings->min_amount > ($qt * $price)) {
            $qt += round($settings['base_precision'], $base);
        }

        $side = $request instanceof OrderLongRequest ? 'Buy' : 'Sell';
        $type = 'Limit';
        $response = $client->order('spot', $request->coin, $side, $type, (string)$qt, $params);
        print_r($response);
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
}