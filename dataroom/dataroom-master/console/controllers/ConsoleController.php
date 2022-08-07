<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\log\Logger;

class ConsoleController extends Controller
{
    const DEFAULT_LOG_CATEGORY = 'application';

    protected $logCategory;

    private $lockFile = '';
    private $lockFilePointer;

    /**
     * Output message to STDOUT and to log
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $message
     * @param string $postfix
     * @param int $level
     */
    public function outputMessage($message, $postfix = '', $level = Logger::LEVEL_INFO)
    {
        if (!$this->logCategory) {
            $category = self::DEFAULT_LOG_CATEGORY;
        } else {
            $category = $this->logCategory;
        }

        // Set text color and adjust message if needed
        switch ($level) {
            case Logger::LEVEL_WARNING:
                $color = Console::FG_YELLOW;
                $message = 'WARNING: ' . $message;
                break;

            case Logger::LEVEL_ERROR:
                $color = Console::FG_RED;
                $message = 'ERROR: ' . $message;
                break;

            default:
                $color = Console::FG_BLACK;
        }

        $logMethod = Logger::getLevelName($level);
        Yii::$logMethod($message, $category);

        $this->stdout($message . $postfix, $color);
    }

    /**
     * Locks action
     * @param string $lockFile
     */
    protected function lockAction($lockFile)
    {
        $this->lockFile = $lockFile;

        if ((@file_exists($this->lockFile)) && (!flock(fopen($this->lockFile, "a+b"), LOCK_EX + LOCK_NB))) {
            exit();
        }

        fclose(fopen($this->lockFile, "a+b"));
        $this->lockFilePointer = fopen($this->lockFile, "r+");
        flock($this->lockFilePointer, LOCK_EX + LOCK_NB);
    }

    /**
     * Release action
     */
    protected function releaseAction()
    {
        if (@file_exists($this->lockFile)) {
            flock($this->lockFilePointer, LOCK_UN);
            fclose($this->lockFilePointer);
            @unlink($this->lockFile);
        }
    }
}