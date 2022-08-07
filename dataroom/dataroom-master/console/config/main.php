<?php

use backend\modules\dataroom\Module as DataroomModule;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    is_file(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    is_file(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',

    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            //'migrationPath' => null,

            'migrationNamespaces' => [
                'backend\modules\office\migrations',
            ],
        ],
        'dataroom' => [
            'class' => 'backend\modules\dataroom\controllers\ConsoleController',
        ],
    ],

    'modules' => [
        'notify' => [
            'class' => 'backend\modules\notify\Module',
        ],
    ],

    'components' => [
        'log' => [
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'warning', 'error'],
                    'categories' => [DataroomModule::LOG_CATEGORY],
                    'logFile' => '@app/runtime/logs/dataroom.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 30,
                    'prefix' => function ($message) {
                        return '';
                    },
                    'logVars' => [],
                    'exportInterval' => 1,
                ],
            ],
        ],
        'urlManager' => [
            'baseUrl' => '',
            'hostInfo' => 'https://dev-www.ajassocies.fr',
        ],
        'urlManagerFrontend' => [
            'baseUrl' => '',
            'hostInfo' => 'https://dev-www.ajassocies.fr',
        ],
        'urlManagerBackend' => [
            'baseUrl' => '',
            'hostInfo' => 'https://dev-admin.ajassocies.fr',
        ],
    ],
    'params' => $params,
];
