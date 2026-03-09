<?php

namespace app\neuro\components;

use app\queue\AiRequestLogQueue;
use Yii;

class LoggingMiddleware
{
    private $provider;
    private $log_data = [];

    public function __construct($provider, $log_data)
    {
        $this->provider = $provider;
        $this->log_data = $log_data;
    }

    public function __invoke(callable $handler)
    {
        /**
         * @var \GuzzleHttp\Psr7\Request $request
         */
        return function ($request, array $options) use ($handler) {

            $body = $request->getBody();
            $pos = $body->tell();            // текущая позиция
            $requestContent = $body->getContents();
            $body->rewind();                 // перематываем на начало
            $body->seek($pos);               // возвращаем позицию как была
            return $handler($request, $options)->then(
                function ($response) use ($request, $requestContent) {
                    $body = $response->getBody();
                    $pos = $body->tell();            // текущая позиция
                    $responseContent = $body->getContents();
                    $body->rewind();                 // перематываем на начало
                    $body->seek($pos);               // возвращаем позицию как была
                    $job = new AiRequestLogQueue();
                    $job->request = trim($requestContent);
                    $job->response = trim($responseContent);
                    $job->provider = $this->provider;
                    $job->log_data = $this->log_data;
                    Yii::$app->queueWorkflow->push($job);
                    return $response;
                },
            );
        };
    }
}