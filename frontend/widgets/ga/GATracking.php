<?php

namespace frontend\widgets\ga;

use yii\base\Widget;

/**
 * Google Analytics Tracking
 *
 * @author Anton Komarev <ell@cybercog.su>
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class GATracking extends Widget
{
    /**
     * Render <script></script> 
     * @var bool
     */
    public $omitScriptTag = false;
    
    /**
     * The GA tracking ID
     * @var string
     */
    public $trackingId = null;

    /**
     * Tracking object configuration.
     * @var array
     */
    public $trackingConfig = 'auto';

    /**
     * Anonymize the IP address of the hit (http request) sent to GA.
     * @var bool
     */
    public $anonymizeIp = false;

    /**
     * Output debug information to the console.
     * @var bool
     */
    public $debug = false;

    /**
     * Trace debugging will output more verbose information to the console.
     * @var bool
     */
    public $debugTrace = false;

    /**
     * Plugins list
     * @var array
     */
    public $plugins = [];

    /**
     * Whether to enable widget for 'prod', 'dev', 'test' environments or enable/disable globally using true/false
     * @var bool
     */
    public $enabled = 'prod';

    /**
     * GA script filename
     * @var string
     */
    private $_trackingFilename = 'analytics.js';

    private $_trackingDebugTraceInit = '';

    /**
     * @var array
     */
    private $_viewParams;

    public function init()
    {
        parent::init();

        if ($this->debug) {
            $this->_trackingFilename = 'analytics_debug.js';
        }
        if ($this->debugTrace) {
            $this->_trackingDebugTraceInit = 'window.ga_debug = {trace: true};';
        }

        $this->trackingConfig = json_encode($this->trackingConfig);
        $this->anonymizeIp = json_encode($this->anonymizeIp);
        foreach ($this->plugins as $plugin => &$options) {
            $options = json_encode($options);
        }

        $this->_viewParams = [
            'omitScriptTag' => $this->omitScriptTag,
            'trackingId' => $this->trackingId,
            'trackingConfig' => $this->trackingConfig,
            'trackingFilename' => $this->_trackingFilename,
            'trackingDebugTraceInit' => $this->_trackingDebugTraceInit,
            'fields' => [
                'anonymizeIp' => $this->anonymizeIp
                // :TODO: Add more params
            ],
            // :TODO: Add availability to configure events
            'plugins' => $this->plugins
        ];
    }

    public function run()
    {
        if ($this->enabled === true || $this->enabled === YII_ENV) {
            echo $this->render('tracking', $this->_viewParams);
        }
    }
}
