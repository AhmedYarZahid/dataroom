<?php

namespace frontend\widgets\fbp;

use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Facebook Pixel
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class FBPTracking extends Widget
{
    /**
     * The Facebook Pixel ID
     * @var string
     */
    public $pixelID = null;

    /**
     * Whether to enable widget for 'prod', 'dev', 'test' environments or enable/disable globally using true/false
     * @var bool
     */
    public $enabled = 'prod';

    /**
     * @var array
     */
    private $_viewParams;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->enabled !== true && $this->enabled !== YII_ENV) {
            return false;
        }

        if ($this->pixelID === null) {
            throw new InvalidConfigException('Please provide "pixelID" param.');
        }

        $this->_viewParams = [
            'pixelID' => $this->pixelID,
        ];
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->enabled === true || $this->enabled === YII_ENV) {
            echo $this->render('tracking', $this->_viewParams);
        }
    }
}
