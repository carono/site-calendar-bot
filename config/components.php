<?php
return [
    'urlManager' => [
        'enablePrettyUrl' => true,
        'enableStrictParsing' => true,
        'showScriptName' => false,
        'rules' => [
            '/' => 'site/index',
            'callback' => 'callback/index',
            '<module>/<controller>/<action>' => '<module>/<controller>/<action>',
            '<controller>/<action>' => '<controller>/<action>',
            '<action>' => ''
        ],
    ],
    'formatter' => [
        'class' => \app\components\Formatter::class,
        'defaultTimeZone' => 'Europe/Moscow',
        'datetimeFormat' => 'php:Y-m-d H:i',
        'currencyCode' => 'RUB',
    ],
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
        'defaultRoles' => ['guest', 'user'],
    ],
    'i18n' => [
        'translations' => [
            '*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages',
                'sourceLanguage' => 'en',
            ],
        ],
    ],
    'log' => require 'log.php',
];