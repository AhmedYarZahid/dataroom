<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    is_file(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    is_file(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

$config = [
    'id' => 'app-backend',
    'name' => 'AJA Admin Panel',

    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',

    'bootstrap' => [
        'jsUrlManager'
    ],

    'modules' => [
        'notify' => [
            'class' => 'backend\modules\notify\Module',
        ],
        'staticpage' => [
            'class' => 'backend\modules\staticpage\Module',
        ],
        'parameter' => [
            'class' => 'backend\modules\parameter\Module',
        ],
        'document' => [
            'class' => 'backend\modules\document\Module',
        ],
        'news' => [
            'class' => 'backend\modules\news\Module',
        ],
        'contact' => [
            'class' => 'backend\modules\contact\Module',
        ],
        'trendypage' => [
            //'class' => 'lateos\trendypage\Module',
            'class' => 'backend\modules\trendypage\Module',
            'previewUrl' => 'https://dev-www.ajadataroom.fr/site/trendy-page-preview',
        ],
        'comments' => [
            'class' => 'backend\modules\comments\Module',
        ],
        'metatags' => [
            'class' => 'backend\modules\metatags\Module',
        ],
        'office' => [
            'class' => 'backend\modules\office\Module',
        ],
        'dataroom' => [
            'class' => 'backend\modules\dataroom\Module',
        ],
        'mailing' => [
            'class' => 'backend\modules\mailing\Module',
        ],
        'rbac' => [
            'class' => 'mdm\admin\Module',
            'layout' => '@app/views/layouts/rbac-menu.php',
            'controllerMap' => [
                'assignment' => [
                    //'class' => 'mdm\admin\controllers\AssignmentController',
                    'class' => 'backend\controllers\rbac\AssignmentController',
                    'userClassName' => 'common\models\User',
                    'idField' => 'id',
                    'usernameField' => 'email',
                    'searchClass' => 'common\models\UserSearch',
                ],
                'role' => [
                    'class' => 'backend\controllers\rbac\RoleController',
                ],
                'permission' => [
                    'class' => 'backend\controllers\rbac\PermissionController',
                ],
                'route' => [
                    'class' => 'backend\controllers\rbac\RouteController',
                ],
                'rule' => [
                    'class' => 'backend\controllers\rbac\RuleController',
                ],
                'init' => [
                    'class' => 'backend\controllers\rbac\InitController',
                ]
            ],
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'pslpgPGhuktNL_jS0KpePiFz7kWR_17I',
        ],
        'user' => [
            'identityClass' => 'backend\models\Admin',
            'enableAutoLogin' => true,
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@vendor/lateos/yii2-trendy-page/views' => '@app/modules/trendypage/views',
                ],
            ],
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            //'rbac/*', // add or remove allowed actions to this list
            '*',
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    //$config['modules']['debug'] = 'yii\debug\Module';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
