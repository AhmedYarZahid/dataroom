<?php

namespace app\modules\supervisormanager\components\supervisor;

/**
 * Interface ConnectionInterface
 *
 * @package app\modules\supervisormanager\components\supervisor
 */
interface ConnectionInterface
{
    /**
     * @return mixed
     */
    public function getConnection();

    /**
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function callMethod($method, array $params = []);
}