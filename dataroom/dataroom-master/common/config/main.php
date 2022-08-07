<?php

use kartik\datecontrol\Module;
use kartik\mpdf\Pdf;

return [
    'name' => 'AJA - SITE ET DATAROOM',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    'language' => 'fr',
    'sourceLanguage' => 'en',

    'bootstrap' => [
        'common\components\Bootstrap',
        'log',
    ],

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],

    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'datecontrol' =>  [
            'class' => 'kartik\datecontrol\Module',

            // set your display timezone
            //'displayTimezone' => 'Asia/Kolkata',

            // set your timezone for date saved to db
            //'saveTimezone' => 'UTC',

            // format settings for displaying each date attribute (ICU format example)
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd/MM/yyyy',
                Module::FORMAT_TIME => 'HH:mm:ss a',
                Module::FORMAT_DATETIME => 'dd-MM-yyyy HH:mm:ss a',
            ],

            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:Y-m-d',
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,

            // use ajax conversion for processing dates from display format to save format.
            'ajaxConversion' => false,

            // default settings for each widget from kartik\widgets used when autoWidget is true
            'autoWidgetSettings' => [
                Module::FORMAT_DATE => ['type' => 2, 'pluginOptions' => ['autoclose' => true]], // example
                Module::FORMAT_DATETIME => [], // setup if needed
                Module::FORMAT_TIME => [], // setup if needed
            ],

            // custom widget settings that will be used to render the date input instead of kartik\widgets,
            // this will be used when autoWidget is set to false at module or widget level.
            'widgetSettings' => [
                Module::FORMAT_DATE => [
                    'class' => 'yii\jui\DatePicker', // example
                    'options' => [
                        'dateFormat' => 'php:d-M-Y',
                        //'dateFormat' => 'php:Y-m-d',
                        'options' => ['class'=>'form-control'],
                    ]
                ]
            ]
            // other settings
        ],
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=xxxxxxxxx',
            'username' => 'ajadataroom',
            'password' => 'xxxxxxxxx',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => array(
                'class' => 'Swift_SmtpTransport',
                'host' => 'xxxxxxxxx',
                'username' => '',
                'password' => '',
                // 'encryption' => 'ssl',
                'port' => 25,
                /*'plugins' => [
                    [
                        'class' => 'Swift_Plugins_ThrottlerPlugin',
                        'constructArgs' => [20],
                    ],
                ],*/
            ),
            /*'viewPath' => '@common/mail',
            'useFileTransport' => true,*/
        ],
        'mailjet' => [
            'class' => 'common\extensions\mailjet\Mailer',
            'apikey' => 'xxxxxxxxx',
            'secret' => 'xxxxxxxxx',
            'sender' => 'xxxxxxxxx@xxxxxxxxx'
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                    ],
                ],
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
                'rbac-admin' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                    ],
                ],
            ],
        ],
        'env' => [
            'class' => 'common\extensions\environment\Environment',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        // setup Krajee Pdf component
        'pdf' => [
            'class' => Pdf::classname(),
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'options' => [
                'simpleTables' => true,
                'useSubstitutions' => false,
            ]
        ],
        'urlManager' => [
            'languages' => [], // see configuration in common\components\Bootstrap
            'class' => 'codemix\localeurls\UrlManager',
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'suffix' => '', // set explicitly because of jsUrlManager bug

            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'collapseSlashes' => true,
                'normalizeTrailingSlash' => true,
            ],
        ],
        'urlManagerFrontend' => [
            'languages' => [], // see configuration in common\components\Bootstrap
            'class' => 'codemix\localeurls\UrlManager',
            'hostInfo' => 'https://dev-www.ajassocies.fr',
            //'baseUrl' => '', // in case of using one domain
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,

            'rules' => [
                'my-profile' => 'dataroom/user/my-profile',
                'my-rooms' => 'dataroom/user/my-rooms',
                'login' => 'dataroom/user/login',
                'logout' => 'dataroom/user/logout',
                'request-password-reset' => 'dataroom/user/request-password-reset',
                'reset-password' => 'dataroom/user/reset-password',
                'one-time-login' => 'dataroom/user/one-time-login',

                'contactez-nous' => 'site/contact',
                'actualites' => 'news',
                'actualites/<id:\d+>' => 'news/view',
                'actualites/<category:communications|media>' => 'news/category',

                'dataroom/companies/room/<id:\d+>' => 'dataroom/companies/view-room',
                'dataroom/companies/room/<id:\d+>/update' => 'dataroom/companies/update-room',
                'dataroom/companies/room/<id:\d+>/proposal' => 'dataroom/companies/proposal',
                'dataroom/companies/room/<id:\d+>/get-access' => 'dataroom/companies/get-access',
                'dataroom/companies/room/<id:\d+>/documents' => 'dataroom/companies/documents',
                'dataroom/companies/room/<id:\d+>/create-document' => 'dataroom/companies/create-document',

                [
                    'class' => 'frontend\components\TrendyPageUrlRule',
                ],

                [
                    'pattern' => 'sitemap',
                    'route' => 'sitemap/default/index',
                    'suffix' => '.xml'
                ],
            ],

            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'collapseSlashes' => true,
                'normalizeTrailingSlash' => true,
            ],
        ],
        'urlManagerBackend' => [
            'languages' => [], // see configuration in common\components\Bootstrap
            'class' => 'codemix\localeurls\UrlManager',
            'hostInfo' => 'https://dev-admin.ajassocies.fr',
            // 'baseUrl' => '/admin', // in case of using one domain
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,

            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'collapseSlashes' => true,
                'normalizeTrailingSlash' => true,
            ],
        ],
        'jsUrlManager' => [
            'class' => \dmirogin\js\urlmanager\JsUrlManager::class,
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'converter' => [
                'class' => 'nizsheanez\assetConverter\Converter',
                //'class' => 'frontend\components\Converter',
                // TODO: check why on dev server after each assets publication old files was also modified and have the same mtime as new files
                'force' => false, // true : If you want convert your sass/less each time without time dependency
                'destinationDir' => '.', //at which folder of @webroot put compiled files
                'parsers' => [
                    'less' => [
                        'class' => 'nizsheanez\assetConverter\Less',
                        'output' => 'css', // parsed output file type
                        'options' => [
                            'auto' => true, // optional options
                        ]
                    ]
                ]
                /*'class' => 'yii\web\AssetConverter',
                'commands' => [
                    'less' => ['css', '/usr/local/bin/lessc {from} > {to} --no-color'],
                ],*/
            ]
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => 'AuthItem',
            'itemChildTable' => 'AuthItemChild',
            'assignmentTable' => 'AuthAssignment',
            'ruleTable' => 'AuthRule',
            'defaultRoles' => ['superadmin', 'admin', 'manager', 'user', 'anonymous'],
        ],
        'formatter' => [
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'locale' => 'fr_FR.UTF-8',
            //'dateFormat' => 'long'
            'dateFormat' => 'php:d F Y',
            'datetimeFormat' => 'php:d F Y H:i',
            'timeZone' => 'UTC'
        ],

        'newsletterManager' => common\components\managers\NewsletterManager::class,
        'documentManager' => common\components\managers\DocumentManager::class,
    ],
];
