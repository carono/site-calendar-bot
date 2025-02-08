<?php

use app\components\Bot;
use carono\yii2log\TelegramTarget;

return [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['error', 'warning'],
        ],
        [
            'class' => 'yii\log\FileTarget',
            'categories' => ['telegram-bot'],
            'logFile' => '@app/runtime/logs/telegram-bot.log',
            'logVars' => [],
            'levels' => ['error', 'warning', 'info'],
        ],
        [
            'class' => TelegramTarget::class,
            'categories' => ['telegram'],
            'chatId' => '85220320',
            'sendMessage' => function ($message, $chatId) {
                $token = Yii::$app->params['telegram']['token'];
                $bot = new Bot();
                $bot->token = $token;
                $bot->getClient()->sendMessage($chatId, $message);
            },
            'logVars' => [],
            'levels' => ['error'],
        ],
        [
            'class' => 'yii\log\FileTarget',
            'categories' => ['market'],
            'logFile' => '@app/runtime/logs/market.log',
            'logVars' => [],
            'levels' => ['error', 'warning', 'info'],
        ],
    ],
];