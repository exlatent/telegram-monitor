<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'telegram-monitor',
    'name' => 'Telegram Monitor',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'UTC',
    'bootstrap' => ['log', 'queue'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@app'   => dirname(__DIR__) . '/src',
    ],
    'controllerNamespace' => 'app\Presentation\Controllers',
    'components' => [
        'request' => [
            'cookieValidationKey' => 'H5lid7e34tsyG8t0GXYOjix1VIksbP1n',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => getenv('REDIS_HOSTNAME') ?: 'localhost',
                'port' => getenv('REDIS_PORT') ?: 6379,
                'database' => getenv('REDIS_DATABASE') ?: 0,
            ]
        ],
        'user' => [
            'identityClass' => 'app\Infrastructure\Persistence\UserRecord',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'formatter' => [
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => "php:d.m.Y H:i:s P",
            'timeFormat' => 'php:H:i:s',
            'defaultTimeZone' => 'UTC',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'site/index',
                'login' => 'site/login',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            'redis' => [
                'hostname' => getenv('REDIS_HOSTNAME') ?: 'localhost',
                'port' => getenv('REDIS_PORT') ?: 6379,
                'database' => getenv('REDIS_DATABASE') ?: 0,
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
