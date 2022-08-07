<?php

namespace console\controllers;

class DaemonExampleController extends ConsoleController
{
    /**
     * Test daemon
     */
    public function actionTest()
    {
        $shallStopWorking = false;

        // signal for supervisord to stop daemon
        pcntl_signal(SIGTERM, function () use (&$shallStopWorking) {
            echo "Received SIGTERM\n";
            $shallStopWorking = true;
        });

        // handler for ctrl+c (to test via console)
        pcntl_signal(SIGINT,  function () use (&$shallStopWorking) {
            echo "Received SIGINT\n";
            $shallStopWorking = true;
        });

        echo "Started\n";

        while (!$shallStopWorking) {
            for ($i = 0; $i < 10; $i += 1) sleep(1);
            echo "Slept for ten seconds\n";

            // process signals at the end
            pcntl_signal_dispatch();
        }

        echo "Finished\n";
    }
}