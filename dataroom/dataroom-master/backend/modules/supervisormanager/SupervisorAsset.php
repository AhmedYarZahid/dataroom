<?php

namespace app\modules\supervisormanager;

use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\web\View;

class SupervisorAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@supervisormanager/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];


    /**
     * @inheritdoc
     */
    public static function register($view)
    {
        $config = [
            'urls' => [
                'supervisorControl' => Url::to(['default/supervisor-control']),
                'processControl' => Url::to(['default/process-control']),
                'groupControl' => Url::to(['default/group-control']),
                'processConfigControl' => Url::to(['default/process-config-control']),
                'countGroupProcesses' => Url::to(['default/count-group-processes']),
                'getProcessLog' => Url::to(['default/get-process-log']),
            ]
        ];

        $view->registerJs('var supervisorManagerConfig = ' . json_encode($config) . ';', View::POS_HEAD);

        return parent::register($view);
    }
}