<?php

namespace app\clients\bybit;

class Client extends \carono\rest\Client
{
    public $url = 'https://api.bybit.com/v5';
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
                'X-BAPI-TIMESTAMP' => time() * 1000,
                'X-BAPI-RECV-WINDOW' => 5000,
//                'proxy' => '192.168.1.254:8888'
            ]
        ];
    }

    public function prepareData(array $data)
    {
        $signature = hash_hmac('sha256', json_encode($data), $this->secret);
        $this->_guzzleOptions['headers']['X-BAPI-SIGN'] = $signature;
        return parent::prepareData($data);
    }

    public function getTimestamp()
    {
        return $this->getContent('market/time');
    }

    public function getOrder()
    {
        $data = [
            'category' => 'option',
            'symbol' => 'BTC-29JUL22-25000-C'
        ];
        return $this->getContent('order/realtime', $data);
    }
}