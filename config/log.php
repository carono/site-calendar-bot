<?php
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
    ],
];