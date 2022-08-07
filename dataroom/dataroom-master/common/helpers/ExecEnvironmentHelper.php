<?php

namespace common\helpers;

/**
 * ExecEnvironmentHelper represents the data needed to do manipulations with execution environment
 * 
 */
class ExecEnvironmentHelper
{
    /**
     * Get user IP
     * 
     * @param boolean $asInteger return type
     * @return string
     */
    public static function getUserIp($asInteger = false)
    {
        $ip = self::getIp();

        return $asInteger ? ip2long($ip) : $ip;
    }

    protected static function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false){
                        return $ip;
                    }
                }
            }
        }
    }

}

?>