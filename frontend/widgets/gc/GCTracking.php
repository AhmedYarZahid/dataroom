<?php

namespace frontend\widgets\gc;

use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Google Conversion Tracking
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class GCTracking extends Widget
{
    /**
     * The GC conversion ID
     * @var integer
     */
    public $conversionID = null;

    /**
     * The GC conversion label
     * @var string
     */
    public $conversionLabel = null;

    /**
     * The GC conversion value
     * @var float
     */
    public $conversionValue = null;

    /**
     * The GC conversion currency
     * @var string
     */
    public $conversionCurrency = null;

    /**
     * The GC conversion language
     * @var string
     */
    public $conversionLanguage = 'en';

    /**
     * The GC conversion format
     * @var string
     */
    public $conversionFormat = '3';

    /**
     * The GC conversion color
     * @var string
     */
    public $conversionColor = 'ffffff';

    /**
     * The GC conversion color
     * @var string
     */
    public $remarketingOnly = false;

    /**
     * Whether to enable widget for 'prod', 'dev', 'test' environments or enable/disable globally using true/false
     * @var bool
     */
    public $enabled = 'prod';

    /**
     * Whether to return only image tag
     * @var bool
     */
    public $onlyImageTag = false;

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

        if ($this->conversionID === null) {
            throw new InvalidConfigException('Please provide "conversionID" param.');
        } elseif ($this->conversionLabel === null) {
            throw new InvalidConfigException('Please provide "conversionLabel" param.');
        }

        $this->_viewParams = [
            'conversionID' => $this->conversionID,
            'conversionLabel' => $this->conversionLabel,
            'conversionLanguage' => $this->conversionLanguage,
            'conversionColor' => $this->conversionColor,
            'conversionFormat' => $this->conversionFormat,
            'conversionValue' => $this->conversionValue,
            'conversionCurrency' => $this->conversionCurrency,
            'remarketingOnly' => $this->remarketingOnly,
            'onlyImageTag' => $this->onlyImageTag,
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
