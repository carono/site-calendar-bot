<?php

namespace app\neuro\components;

use GuzzleHttp\HandlerStack;
use yii\helpers\ArrayHelper;

class Client extends \GuzzleHttp\Client
{

    public function __construct(array $config = [])
    {
        $log_data = ArrayHelper::remove($config, 'log_data', []);
        $provider = ArrayHelper::remove($config, 'provider');
        if (empty($config['handler'])) {
            $stack = HandlerStack::create();
            $stack->push(new \app\neuro\components\LoggingMiddleware($provider, $log_data));
            $config['handler'] = $stack;
        }
//        $config['proxy'] = '192.168.1.254:8888';
//        $config['verify'] = false;
        parent::__construct($config);
    }
}