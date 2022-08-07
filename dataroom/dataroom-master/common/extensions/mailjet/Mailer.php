<?php

namespace common\extensions\mailjet;

use Mailjet\Resources;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\base\UserException;
use yii\mail\BaseMailer;
use yii\validators\UrlValidator;

/**
 * Contains the Mailjet Mailer class
 *
 * @package common/extensions/mailjet
 */
class Mailer extends BaseMailer
{
    public $apiVersion = 'v3'; // v3.1 doesn't work for anything except /send

    /**
     * @var string message default class name.
     */
    public $messageClass = 'common\extensions\mailjet\Message';

    private $_mailjet;
    private $_apikey;
    private $_secret;
    private $_templateLanguage = true;
    private $_sender;

    /**
     *  Set your tracking event's url
     *  bsp:
     *  [
     *      'bounce' => 'http://yoururl.com/tracking/bounce',
     *  ]
     */
    private $_tracking;

    private $_allowedTrackingEvents = [
        'sent',
        'open',
        'click',
        'bounce',
        'spam',
        'blocked',
        'unsub',
    ];

    /**
     * @var $_response \Mailjet\Response
     */
    private $_response;

    public function init()
    {
        if (!$this->_apikey) {
            throw new InvalidConfigException(sprintf('"%s::apikey" cannot be null.', get_class($this)));
        }

        if (!$this->_secret) {
            throw new InvalidConfigException(sprintf('"%s::secret" cannot be null.', get_class($this)));
        }

        if (!$this->_sender) {
            throw new InvalidConfigException(sprintf('"%s::sender" cannot be null.', get_class($this)));
        }

        try {
            $this->createMailjet();
        } catch (\Exception $exc) {
            \Yii::error($exc->getMessage());
            throw new \Exception('an error occurred with your mailer. Please check the application logs.', 500);
        }
    }

    /**
     * Sets the API secret key for Mailjet
     *
     * @param string $secret
     * @throws InvalidConfigException
     */
    public function setSecret($secret)
    {
        if (!is_string($secret)) {
            throw new InvalidConfigException(sprintf('"%s::secret" should be a string, "%s" given.', get_class($this), gettype($secret)));
        }
        $trimmedSecret = trim($secret);
        if (!strlen($trimmedSecret) > 0) {
            throw new InvalidConfigException(sprintf('"%s::secret" length should be greater than 0.', get_class($this)));
        }
        $this->_secret = $trimmedSecret;

    }

    /**
     * Sets the API key for Mailjet
     *
     * @param string $apikey the Mailjet API key
     * @throws InvalidConfigException
     */
    public function setApikey($apikey)
    {
        if (!is_string($apikey)) {
            throw new InvalidConfigException(sprintf('"%s::apikey" should be a string, "%s" given.', get_class($this), gettype($apikey)));
        }
        $trimmedApikey = trim($apikey);
        if (!strlen($trimmedApikey) > 0) {
            throw new InvalidConfigException(sprintf('"%s::apikey" length should be greater than 0.', get_class($this)));
        }
        $this->_apikey = $trimmedApikey;
    }

    /**
     * Set sender
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $sender
     */
    public function setSender($sender)
    {
        $this->_sender = $sender;
    }

    /**
     * Get sender
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return mixed
     */
    public function getSender()
    {
        return $this->_sender;
    }

    /**
     *  Create the Mailjet Object
     */
    public function createMailjet()
    {
        $mj = new \Mailjet\Client($this->_apikey, $this->_secret, true, ['version' => $this->apiVersion]);

        $this->_mailjet = $mj;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        $recipients = [];

        foreach ($message->to as $email => $name) {
            $newRecipient = [];

            if (empty($email)) {
                throw new NotSupportedException('Email of recipient not provided.');
            }

            $newRecipient['Email'] = $email;

            if (!empty($name)) {
                $newRecipient['Name'] = $name;
            }

            $recipients[] = $newRecipient;
        }

        $body = ['Messages' => []];

        foreach ($recipients as $recipient) {
            $messageEntry = [
                'From' => $message->getFrom(),
                'To' => [$recipient],
                'Subject' => $message->subject,
                'TextPart' => $message->textBody,
                'HTMLPart' => $message->htmlBody,
                'TemplateLanguage' => $this->_templateLanguage,
            ];

            if ($variables = $message->getVariablesByKey($recipient['Email'])) {
                $messageEntry['Variables'] = $variables;
            }

            $body['Messages'][] = $messageEntry;
        }

        if ($attachments = $message->getAttachments()) {
            foreach ($body['Messages'] as $key => $value) {
                $body['Messages'][$key]['Attachments'] = $attachments;
            }
        }

        //Adds Reply-To to header
        if (!empty($message->replyTo)) {
            $body['Headers']['Reply-to'] = $message->replyTo;
        }

        if (!empty($message->campaign)) {
            foreach ($body['Messages'] as $key => $value) {
                $body['Messages'][$key]['CustomCampaign'] = $message->campaign;
            }
        }

        $response = $this->_mailjet->post(Resources::$Email, ['body' => $body], ['version' => 'v3.1']);
        $this->_response = $response;

        return $response->success();
    }

    public function setTracking($tracking)
    {
        if (is_array($tracking)) {

            $urlValidator = new UrlValidator;

            foreach ($tracking as $event => $url) {

                if (in_array($event, $this->_allowedTrackingEvents)) {

                    if (!$urlValidator->validate($url)) {
                        throw new InvalidConfigException(sprintf('"%s::%s" should be a url', get_class($this), $event));
                    }

                    $this->_tracking[$event] = $url;
                } else {
                    throw new InvalidConfigException(sprintf('the %s event is not supported', $event));
                }
            }

        } else {
            throw new InvalidConfigException('The trackingActions must be an array');
        }
    }

    public function activateAllTrackings()
    {
        foreach ($this->_tracking as $event => $url) {
            $this->activateTracking($event, $url);
        }

        return true;
    }

    public function activateTracking($event, $url)
    {
        $body = [
            'EventType' => $event,
            'Url' => $url,
        ];

        $response = $this->_mailjet->post(Resources::$Eventcallbackurl, ['body' => $body]);

        if (!$response->success()) {

            $eventCallbackurl = Resources::$Eventcallbackurl;
            $eventCallbackurl[1] = $event;

            $eventExist = $this->_mailjet->get($eventCallbackurl);

            $responseData = $eventExist->getData();

            /* check if is the tracking url the same  */
            if ($responseData[0]['Url'] != $url) {
                throw new UserException('You must clear your old tracking urls first: Yii::$app->mailer->clearAllTrackings(); or Yii::$app->mailer->clearTracking(\'' . $event . '\');');
            }
        }

        return true;
    }

    public function clearAllTrackings()
    {
        foreach ($this->_tracking as $event => $url) {
            $this->clearTracking($event);
        }
    }

    public function clearTracking($event)
    {
        if (!in_array($event, $this->_allowedTrackingEvents)) {
            throw new InvalidConfigException(sprintf('the %s event is not supported', $event));
        }

        $eventCallbackurl = Resources::$Eventcallbackurl;
        $eventCallbackurl[1] = $event;

        $response = $this->_mailjet->delete($eventCallbackurl);
    }

    /**
     * Loads mailjet stats.
     * 
     * @return array Array of campaigns.
     */
    public function getCampaignStats()
    {
        /*$campaignNames = [];
        $response = $this->_mailjet->get(Resources::$Campaign, ['filters' => ['CustomCampaign' => ['LIST_1_1_LOCAL', 'LIST_2_2_LOCAL']]]);
        
        foreach ($response->getData() as $value) {
            $campaignNames[$value['ID']] = $value['CustomValue'];
        }*/

        $campaigns = [];
        $response = $this->_mailjet->get(Resources::$Campaignoverview);

        foreach ($response->getData() as $value) {
            $campaigns[$value['Title']] = $value;
        }

        return $campaigns;
    }

    /**
     * Set value for template language
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param bool $value
     */
    public function setTemplateLanguage($value = true)
    {
        $this->_templateLanguage = $value;
    }
}
