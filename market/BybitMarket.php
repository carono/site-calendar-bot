<?php

namespace app\market;

use app\clients\bybit\Client;
use app\helpers\RoundHelper;
use app\market\order\OrderLongRequest;
use app\market\order\OrderRequest;
use app\models\PvMarketSetting;
use Exception;
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
        $price = $this->getApi()->roundPrice($request->price, $request->coin);
        $params = [
            'price' => (string)($price),
            'stopLoss' => (string)$this->getApi()->roundPrice($request->stop_loss, $request->coin),
            'takeProfit' => (string)$this->getApi()->roundPrice($request->take_profit1, $request->coin),
            'orderLinkId' => uniqid('neuro-'),
//            "tpLimitPrice" => (string)$this->getApi()->roundPrice($request->take_profit1, $request->coin),
//            "slLimitPrice" => (string)$this->getApi()->roundPrice($request->stop_loss, $request->coin),
            'timeInForce' => 'GTC',
            "tpOrderType" => "Market",
            "slOrderType" => "Market"
        ];
        $base = RoundHelper::getPrecisionBase($this->getApi()->getCoinSetting($request->coin)->base_precision);

        $qt = round($request->sum / $price, $base);
        while ($settings->min_amount > ($qt * $price)) {
            $qt += round($settings['base_precision'], $base);
        }

        $side = $request instanceof OrderLongRequest ? 'Buy' : 'Sell';
        $type = 'Limit';
        $response = $client->order('spot', $request->coin, $side, $type, (string)$qt, array_filter($params));
//        Yii::error(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 'telegram');
        if (isset($response->result->orderId)) {
            return (int)$response->result->orderId;
        }
        $request->addError('coin', $response->retMsg);
        return false;
    }

    public function getPrice(string $coin, string $type = self::TYPE_SPOT, string $method = self::METHOD_BUY)
    {
        $client = $this->getClient();
        $response = $client->getOrderbook($type, $coin);
        $result = $response->result;
        if (empty($result->a[0][0])) {
            throw new Exception("Не удалось обновить цену для \"{$coin}\", \"$type\", \"$method\"");
        }
        if ($method == self::METHOD_BUY) {
            return $result->a[0][0];
        }
        return $result->b[0][0];
    }

    public function getOrderInfo($external_id)
    {
        $response = $this->getClient()->getOrderInfo('spot');
//        file_put_contents('2.json', json_encode($response));
//        var_dump(1);
//        exit;
        $result = new OrderInfoDTO();
        $result->status = $response->result->list[0]->orderStatus;
        $result->price = $response->result->list[0]->price;
        return $result;
    }

    protected function orderInfoToDTO($data)
    {
        $item = new OrderInfoDTO();
        $item->id = $data->orderId;
        $item->status = $data->orderStatus;
        $item->price = $data->basePrice;
        $item->takeProfit = $data->takeProfit;
        $item->stopLoss = $data->stopLoss;
        $item->qty = $data->qty;
        $item->symbol = $data->symbol;
        $item->basePrice = $data->basePrice;
        return $item;
    }

    public function getOpenOrders()
    {
        $result = [];
        foreach ($this->getClient()->getOrderInfo('spot')->result->list as $order) {
            $result[] = $this->orderInfoToDTO($order);
        }
        return $result;
    }
}