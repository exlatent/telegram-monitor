<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'telegram-monitor-console',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'UTC',
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\Console\Controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@app'   => dirname(__DIR__) . '/src',
        '@runtime' => dirname(__DIR__) . '/runtime',
        '@webroot' => dirname(__DIR__) . '/web',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => getenv('REDIS_HOSTNAME') ?: 'localhost',
                'port' => getenv('REDIS_PORT') ?: 6379,
                'database' => getenv('REDIS_DATABASE') ?: 0,
            ]
        ],
        'formatter' => [
            'datetimeFormat' => "php:d.m.Y H:i:s (\\U\\T\\C)",
            'defaultTimeZone' => 'UTC',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            'redis' => [
                'hostname' => getenv('REDIS_HOSTNAME') ?: 'localhost',
                'port' => getenv('REDIS_PORT') ?: 6379,
                'database' => getenv('REDIS_DATABASE') ?: 0,
            ],
        ],
    ],
    'params' => $params,
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => '@app/../migrations',
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
