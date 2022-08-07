<?php

namespace backend\modules\mailing\managers;

use common\models\User;
use Yii;
use backend\modules\mailing\models\MailingCampaign;
use backend\modules\mailing\models\MailingList;
use yii\helpers\ArrayHelper;

class CampaignManager
{
    /**
     * @param  MailingCampaign $campaign
     * @return bool Whether the model was saved.
     */
    public function save(MailingCampaign $campaign)
    {
        switch ($campaign->scenario) {
            case $campaign::SCENARIO_CREATE_OR_UPDATE:
                return $this->createOrUpdate($campaign);
            
            case $campaign::SCENARIO_TEST_EMAIL:
                return $this->sendTestEmail($campaign);

            case $campaign::SCENARIO_SEND:
                return $this->send($campaign);
        }
    }

    /**
     * @param  MailingCampaign $campaign
     * @return bool Whether the model was saved.
     */
    public function createOrUpdate(MailingCampaign $campaign)
    {
        return $campaign->save();
    }

    /**
     * @param  MailingCampaign $campaign
     * @param bool $saveToDB
     * @return bool Whether an email was sent.
     */
    public function sendTestEmail(MailingCampaign $campaign, $saveToDB = true)
    {
        if ($saveToDB && !$this->createOrUpdate($campaign)) {
            return false;
        }

        $message = Yii::$app->mailjet->compose()
            ->setFrom($campaign->sender)
            ->setTo([$campaign->testTo => ''])
            ->setSubject($campaign->subject)
            ->setHtmlBody($campaign->getFullBody())
            ->setTextBody(strip_tags($campaign->body))
            ->setCampaign($campaign->uniqueName . '_TEST');

        $sent = $message->send();

        if (!$sent) {
            Yii::error(Yii::$app->mailjet->response->getData(), 'mailing');
        }

        //dd(Yii::$app->mailjet->response->getData());

        return $sent;
    }

    /**
     * @param  MailingCampaign $campaign
     * @return bool Whether an email was sent.
     */
    public function send(MailingCampaign $campaign)
    {
        if (!$this->createOrUpdate($campaign)) {
            return false;
        }

        $list = MailingList::findOne($campaign->listID);
        
        $message = Yii::$app->mailjet->compose()
            ->setFrom($campaign->sender)
            ->setTo($list->getRecipients())
            ->setSubject($campaign->subject)
            ->setHtmlBody($campaign->getFullBody())
            ->setTextBody(strip_tags($campaign->body))
            ->setVariables($list->getRecipients(true))
            ->setCampaign($campaign->uniqueName);

        $sent = $message->send();

        if (!$sent) {
            Yii::error(Yii::$app->mailjet->response->getData(), 'mailing');
        } else {
            $campaign->sentDate = date('Y-m-d H:i:s');
            $campaign->status = $campaign::STATUS_SENT;
            $campaign->save();
        }

        print_r(Yii::$app->mailjet->response->getData());
exit();
        return $sent;
    }

    /**
     * Sends email to several users
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param MailingCampaign $campaign
     * @return bool Whether an email was sent.
     */
    public function sendToUsers(MailingCampaign $campaign)
    {
        $usersList = User::find()->where(['id' => $campaign->recipientIDs])->all();
        $recipientsList = ArrayHelper::map($usersList, 'email', function (User $model) { return $model->getFullname(); });

        $variables = [];
        foreach ($usersList as $user) {
            $variables[$user->email] = [
                'unsubscribeLink' => $user->getUnsubscribeLink()
            ];
        }

        $message = Yii::$app->mailjet->compose()
            ->setFrom($campaign->sender)
            ->setTo($recipientsList)
            ->setSubject($campaign->subject)
            ->setHtmlBody($campaign->getFullBody())
            ->setTextBody(strip_tags($campaign->body))
            ->setVariables($variables);
            //->setCampaign($campaign->uniqueName);

        $sent = $message->send();

        if (!$sent) {
            Yii::error(Yii::$app->mailjet->response->getData(), 'mailing');
        }

        return $sent;
    }

    /**
     * Returns array of errors received from Mailjet.
     * 
     * @return array
     */
    public function getEmailErrors()
    {
        if (empty(Yii::$app->mailjet->response)) {
            return [];
        }

        $data = Yii::$app->mailjet->response->getData();
//        dd($data);
        $errors = [];

        foreach ($data['Messages'] as $message) {
            if (!empty($message['Errors'])) {
                foreach ($message['Errors'] as $error) {
                    $errors[] = $error['ErrorMessage'];
                }
            }
        }

        return $errors;
    }
}