<?php

namespace common\extensions\environment;

/**
 * Environment class that used to get data depending on environment (dev, prod or test)
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class Environment extends \yii\base\Component
{
    /**
     * Prefix which will be added to strings (email subject, site title etc.) in case project run in dev mode
     * 
     * @var string
     */
    public $devEnvPrefix = '[DEV] ';

    /**
     * Prefix which will be added to strings (email subject, site title etc.) in case project run in test mode
     *
     * @var string
     */
    public $testEnvPrefix = '[TEST] ';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * Returns string with prefix (in case project run in dev/test mode)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $string
     * @return string
     */
    private function getStringWithEnvPrefix($string)
    {
        switch (YII_ENV) {
            case 'dev':
               $prefix = $this->devEnvPrefix;
                break;

            case 'test':
                $prefix = $this->testEnvPrefix;
                break;

            default:
                $prefix = '';
        }

        return $prefix . $string;
    }

    
    // --- Main methods --- //

    /**
     * Get processed email subject
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $subject
     * @return string
     */
    public function getEmailSubject($subject)
    {
        return $this->getStringWithEnvPrefix($subject);
    }

    /**
     * Get processed site title
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $title
     * @return string
     */
    public function getSiteTitle($title)
    {
        return $this->getStringWithEnvPrefix($title);
    }

}