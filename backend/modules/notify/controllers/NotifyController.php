<?php

namespace backend\modules\notify\controllers;

use Yii;
use yii\console\Controller;
use backend\modules\notify\models\NotifySendList;
use backend\modules\notify\models\Notify;
use yii\helpers\Console;

class NotifyController extends Controller
{
    const EMAILS_LIMIT = 500;
    const MAX_SEND_ATTEMPTS = 3;
    const THROTTLER_RATE_CORRECTION = 40;

    private $lockFile = '';
    private $lockFilePointer;

    /**
     * Performs sending of notifications from the DB queue
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionSend()
    {
        // Check locked file to prevent multiple scripts running
        $this->lockAction(dirname(__FILE__) . '/../tmp/notify.lock');

        // Send notifications
        $this->stdout("Sending notifications...\n");

        $successCount = 0;
        $failedCount = 0;

        /*$alertsLimitPerMinute = floor(Parameter::getMaxEmailsNumberSentPerHour() / 60);
        $alertsList = NotifySendList::getListToSend($alertsLimitPerMinute);*/

        $notifiesList = NotifySendList::getListToSend(static::EMAILS_LIMIT);
        foreach ($notifiesList as $notify) {
            $this->stdout("Sending notification to <" . $notify->email . "> ");

            $result = Notify::sendEmail(
                explode(';', $notify->email),
                $notify->subject,
                $notify->body,
                $notify->notifyID,
                $notify->eventID,
                $notify->userID,
                $notify->attachedFiles ? unserialize($notify->attachedFiles) : array() //,
                //array('throttlerPlugin' => array('rate' => $alertsLimitPerMinute + self::THROTTLER_RATE_CORRECTION))
            );

            if (!$result && $notify->failedAttemptsCount + 1 < self::MAX_SEND_ATTEMPTS) {
                // Increase failed attempts count
                NotifySendList::increaseFailedAttemptsCount($notify->id);
            } else {
                // Delete record from queue
                NotifySendList::deleteRecord($notify->id);
            }

            if ($result) {
                $successCount++;
                $this->stdout("- OK\n", Console::FG_GREEN);
            } else {
                $failedCount++;
                $this->stdout("- FAILED\n", Console::FG_RED);
            }
        }

        $this->stdout("Total notifications sent: " . ($successCount + $failedCount) . ". Success: $successCount. Failed: $failedCount.\n", Console::BOLD);

        // Release lock
        $this->releaseAction();

        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * Locks action
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