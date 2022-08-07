<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    is_file(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    is_file(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

$config = [
    'id' => 'app-frontend',
    'name' => 'AJA',

    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'bootstrap' => [
        'jsUrlManager'
    ],

    'modules' => [
        'sitemap' => [
            'class' => 'himiklab\sitemap\Sitemap',
            'models' => [
                // your models
                //'backend\modules\news\models\News',

                // or configuration for creating a behavior
                [
                    'class' => 'backend\modules\news\models\News',
                    'behaviors' => [
                        'sitemap' => [
                            'class' => \himiklab\sitemap\behaviors\SitemapBehavior::className(),
                            'scope' => function (\backend\modules\news\models\NewsQuery $model) {
                                $model->published();
                            },
                            'dataClosure' => function (\backend\modules\news\models\News $model) {
                                $result = [
                                    'loc' => \yii\helpers\Url::to(['/news/view', 'id' => $model->id], true),
                                    'lastmod' => strtotime($model->publishDate),
                                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                                    'priority' => 0.8,
                                ];

                                if (date('Y-m-d', strtotime('+2 days', strtotime($model->publishDate))) >= date('Y-m-d')) {
                                    $result['news'] = [
                                        'publication' => [
                                            'name' => 'Yii2Default: Le Blog',
                                            'language' => 'fr',
                                        ],
                                        'genres' => 'Blog, UserGenerated',
                                        'publication_date' => $model->publishDate, // 'YYYY-MM-DDThh:mm:ssTZD' or 'YYYY-MM-DD' format
                                        'title' => trim($model->title),
                                        'keywords' => 'yii2, default',
                                        //'stock_tickers'     => 'NASDAQ:A, NASDAQ:B',
                                    ];
                                }

                                if ($imageUrl = $model->getImageUrl()) {
                                    $result['images'] = [
                                        [
                                            'loc' => $imageUrl,
                                            'caption' => trim($model->title),
                                            'geo_location' => 'France',
                                            'title' => trim($model->title),
                                            //'license'       => 'http://example.com/license',
                                        ],
                                    ];
                                }

                                return $result;
                            }
                        ],
                    ],
                ],
                [
                    'class' => 'lateos\trendypage\models\TrendyPage',
                    'behaviors' => [
                        'sitemap' => [
                            'class' => \himiklab\sitemap\behaviors\SitemapBehavior::className(),
                            'scope' => function (\lateos\trendypage\models\TrendyPageQuery $model) {
                                $model->removed(false);
                            },
                            'dataClosure' => function (\lateos\trendypage\models\TrendyPage $model) {
                                return [
                                    'loc' => \yii\helpers\Url::to(['/site/trendy-page', 'id' => $model->id], true),
                                    'lastmod' => strtotime($model->updatedDate),
                                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                                    'priority' => 0.8
                                ];
                            }
                        ],
                    ],
                ],
            ],
            'urls' => [
                // your additional urls
                [
                    'loc' => ['/'],
                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8,
                ],
                [
                    'loc' => ['/news'],
                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8,
                ],
                [
                    'loc' => ['/site/contact'],
                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8,
                ],
                [
                    'loc' => ['/site/login'],
                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.5,
                ],
                [
                    'loc' => ['/site/request-password-reset'],
                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.5,
                ],
            ],
            'enableGzip' => false, // default is false
            'cacheExpire' => 60, // 1 minute. Default is 24 hours
        ],
        'dataroom' => 'frontend\modules\dataroom\Module',
    ],

    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'L0hEGHnkK3vrujzFho1Hzl8lBWWBgYPu',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => '/login',

            // For RESTful API:
            /*'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null*/
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
        'urlManager' => [
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
            ]

            // For RESTful API:
            /*'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => true,
                    'controller' => ['user'],
                    //'extraPatterns' => [
                    //    'GET ask-new' => 'ask-new',
                    //],
                ],
            ],*/
        ],

        // For RESTful API:
        /*'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],*/
    ],
    'params' => $params,
];

/* Include debug functions */
require_once(__DIR__ . '/functions.php');


return $config;
