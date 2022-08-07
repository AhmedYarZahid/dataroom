<?php

namespace app\modules\supervisormanager;

use app\modules\supervisormanager\components\supervisor\ConnectionInterface;
use app\modules\supervisormanager\components\supervisor\Supervisor;
use yii\base\Event;
use Zend\XmlRpc\Client;

/**
 * @property array supervisorConnection
 */
class Module extends \yii\base\Module
{
    /**
     * @var array Supervisor client authenticate data.
     */
    public $authData = [];

    /**
     * @var string
     */
    public $controllerNamespace = 'app\modules\supervisormanager\controllers';

    public function init()
    {
        parent::init();

        \Yii::setAlias('@supervisormanager', $this->getBasePath());

        Event::on(Supervisor::className(), Supervisor::EVENT_CONFIG_CHANGED,
            function () {
                exec('supervisorctl update', $output, $status);
            }
        );

        \Yii::configure($this, require(__DIR__ . '/config.php'));

        $this->params['supervisorConnection'] = array_merge(
            $this->params['supervisorConnection'], $this->authData
        );

        $this->registerIoC();
    }

    protected function registerIoC()
    {
        \Yii::$container->set(
            Client::class,
            function () {
                return new Client(
                    $this->params['supervisorConnection']['url']
                );
            }
        );

        \Yii::$container->set(
            ConnectionInterface::class,
            $this->params['supervisorConnection']
        );
    }
}
