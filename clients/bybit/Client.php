<?php

namespace app\clients\bybit;

use Yii;

class Client extends \carono\rest\Client
{
    public $url = 'https://api.bybit.com/v5';
//    public $url = 'https://api-testnet.bybit.com/v5';
    public $type = self::TYPE_JSON;
    public $output_type = self::TYPE_JSON;
    public $postDataInBody = true;

    public $token;
    public $secret;

    public function customGuzzleOptions()
    {
        return [
            'headers' => [
                'X-BAPI-API-KEY' => $this->token,
                'X-BAPI-RECV-WINDOW' => 5000,
            ],
//            'proxy' => '192.168.1.254:8888',
//            'verify' => false
        ];
    }

    public function beforeGetContent($data, $options)
    {
        $timestamp = time() * 1000;
        if ($this->method == 'POST') {
            $params_for_signature = $timestamp . $this->token . "5000" . json_encode($data);
        } else {
            $params_for_signature = $timestamp . $this->token . "5000" . http_build_query($data);
        }

        $signature = hash_hmac('sha256', $params_for_signature, $this->secret);
        $this->_guzzleOptions['headers']['X-BAPI-SIGN'] = $signature;
        $this->_guzzleOptions['headers']['X-BAPI-TYPE'] = 2;
        $this->_guzzleOptions['headers']['X-BAPI-TIMESTAMP'] = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->getContent('market/time');
    }

    public function getOrderbook($category, $symbol)
    {
        $data = [
            'category' => $category,
            'symbol' => $symbol
        ];
        return $this->getContent('market/orderbook', $data);
    }

    public function walletBalance($coin = null)
    {
        $data = [
            'coin' => $coin,
            'accountType' => 'UNIFIED'
        ];
        return $this->getContent('account/wallet-balance', $data);
    }

    public function order($category, $symbol, $side, $orderType, $qty, $params = [])
    {
        $data = array_merge([
            'category' => (string)$category,
            'symbol' => (string)$symbol,
            'side' => (string)$side,
            'orderType' => (string)$orderType,
            'qty' => (string)$qty,
        ], $params);
//        Yii::error(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 'telegram');
        return $this->getContent('order/create', $data, ['method' => 'POST']);
    }

    public function instrumentsInfo($category, $symbol = null)
    {
        $data = [
            'category' => $category,
            'symbol' => $symbol
        ];
        return $this->getContent('market/instruments-info', $data);
    }

    public function getOrderInfo($category, $params = [])
    {
        $data = array_merge([
            'category' => $category
        ], $params);
        return $this->getContent('order/realtime', $data);
    }

    public function getOrderHistory($category, $params = [])
    {
        $data = array_merge([
            'category' => $category
        ], $params);
        return $this->getContent('order/history', $data);
    }
}