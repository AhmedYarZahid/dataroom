<?php

namespace app\modules\supervisormanager\tests\components\supervisor;

use app\modules\supervisormanager\components\supervisor\Connection;
use app\modules\supervisormanager\components\supervisor\Supervisor;

class SupervisorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | Supervisor
     */
    protected $supervisor;

    public function setUp()
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->supervisor = $this->getMock(
            Supervisor::class, null, [$connection]
        );
    }

    public function testConfigChangedEvent()
    {
        $this->assertInternalType(
            'int', $this->supervisor->configChangedEvent()
        );
    }
}