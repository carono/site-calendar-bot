<?php

namespace app\components;

use yii\base\BootstrapInterface;

class AppBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        \Yii::$classMap['OpenAI\Responses\Meta\MetaInformation'] = dirname(__DIR__) . '/override/MetaInformation.php';
        \Yii::$classMap['TelegramBot\Api\BotApi'] = dirname(__DIR__) . '/override/BotApi.php';
    }
}
