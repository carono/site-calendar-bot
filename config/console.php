<?php

use yii\helpers\ArrayHelper;

$params = ArrayHelper::merge(require __DIR__ . '/params.php', file_exists(__DIR__ . '/params-local.php') ? require __DIR__ . '/params-local.php' : []);
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', '\app\components\AppBootstrap'],
    'controllerNamespace' => 'app\commands',
    'language' => 'ru',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => ArrayHelper::merge(require 'components.php', [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => $db,
    ]),
    'modules' => require __DIR__ . '/modules.php',
    'params' => $params,
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'templateFile' => '@app/templates/migration.php',
            'migrationPath' => [
                '@app/migrations',
                '@vendor/yiisoft/yii2/rbac/migrations',
            ]
        ],
        'message' => [
            'class' => 'app\commands\MessageController'
        ],
        'giix' => [
            'class' => 'carono\giix\GiixController',
            'generator' => \app\templates\Generator::class,
            'templatePath' => '@app/templates/giix',
            'exceptTables' => [
                '{{%auth_assignment}}',
                '{{%auth_item}}',
                '{{%auth_item_child}}',
                '{{%auth_rule}}',
                '{{%migration}}'
            ],
        ]
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
