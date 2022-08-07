<?php

namespace common\extensions\mailjet;

use Mailjet\Resources;
use yii\base\InvalidParamException;
use yii\mail\BaseMessage;
use yii\base\Exception;

/**
 * Contains the Message class
 *
 * @package common/extensions/mailjet
 */
class Message extends BaseMessage
{
    public $campaign;

    private $_charset;
    private $_from;
    private $_to;
    private $_replyTo;
    private $_cc;
    private $_bcc;
    private $_subject;
    private $_textBody;
    private $_htmlBody;
    private $_attachments = [];
    private $_variables = [];

    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->_from;
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        if (is_array($from)) {
            $this->_from = [
                'Email' => key($from),
                'Name' => array_shift($from),
            ];
        } else {
            $this->_from['Email'] = $from;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        if (!is_array($to)) {
            $to = [$to => ''];
        }
        $this->_to = $to;

        return $this;
    }

    /**
     * Get variables
     */
    public function getVariables()
    {
        return $this->_variables;
    }

    /**
     * Get variable by key (by email)
     */
    public function getVariablesByKey($key)
    {
        return isset($this->_variables[$key]) ? $this->_variables[$key] : [];
    }

    /**
     * Set mailjet variables (format: array($email1 => $varList1, $email2 => $varList2))
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param array $variables
     * @return $this
     */
    public function setVariables($variables)
    {
        $this->_variables = $variables;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return $this->_replyTo;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->_replyTo = $replyTo;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->_cc;
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        $this->_cc = $cc;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->_bcc;
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        $this->_bcc = $bcc;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;

        return $this;
    }

    /**
     * return the plain text for the mail
     */
    public function getTextBody()
    {
        return $this->_textBody;
    }

    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->_textBody = $text;
        //adding HTML body by default - becouse MailJet service not support only text messages
        if (empty($this->_htmlBody)) {
            $this->_htmlBody = $text;
        }

        return $this;
    }

    /**
     * return the html text for the mail
     */
    public function getHtmlBody()
    {
        return $this->_htmlBody;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->_htmlBody = $html;

        return $this;
    }

    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * @return array|null list of attachments
     */
    public function getAttachments()
    {
        if (empty($this->_attachments)) {
            return null;
        } else {
            $attachments = array_map(function($attachment) {
                $item = [
                    'ContentType' => $attachment['ContentType'],
                    'Filename' => $attachment['Name'],
                    'Base64Content' => $attachment['Content'],
                ];
                if (isset($attachment['ContentID']) === true) {
                    $item['ContentID'] = $attachment['ContentID'];
                }
                return $item;
            }, $this->_attachments);

            return $attachments;
        }
    }

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        $attachment = [
            'Content' => base64_encode(file_get_contents($fileName))
        ];
        if (!empty($options['fileName'])) {
            $attachment['Name'] = $options['fileName'];
        } else {
            $attachment['Name'] = pathinfo($fileName, PATHINFO_BASENAME);
        }
        if (!empty($options['contentType'])) {
            $attachment['ContentType'] = $options['contentType'];
        } else {
            $attachment['ContentType'] = 'application/octet-stream';
        }
        $this->_attachments[] = $attachment;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        $attachment = [
            'Content' => base64_encode($content)
        ];
        if (!empty($options['fileName'])) {
            $attachment['Name'] = $options['fileName'];
        } else {
            throw new InvalidParamException('Filename is missing');
        }
        if (!empty($options['contentType'])) {
            $attachment['ContentType'] = $options['contentType'];
        } else {
            $attachment['ContentType'] = 'application/octet-stream';
        }
        $this->_attachments[] = $attachment;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        $embed = [
            'Content' => base64_encode(file_get_contents($fileName))
        ];
        if (!empty($options['fileName'])) {
            $embed['Name'] = $options['fileName'];
        } else {
            $embed['Name'] = pathinfo($fileName, PATHINFO_BASENAME);
        }
        if (!empty($options['contentType'])) {
            $embed['ContentType'] = $options['contentType'];
        } else {
            $embed['ContentType'] = 'application/octet-stream';
        }
        $embed['ContentID'] = 'cid:' . uniqid();

        $this->_attachments[] = $embed;

        return $embed['ContentID'];
    }

    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        $embed = [
            'Content' => base64_encode($content)
        ];
        if (!empty($options['fileName'])) {
            $embed['Name'] = $options['fileName'];
        } else {
            throw new InvalidParamException('Filename is missing');
        }
        if (!empty($options['contentType'])) {
            $embed['ContentType'] = $options['contentType'];
        } else {
            $embed['ContentType'] = 'application/octet-stream';
        }
        $embed['ContentID'] = 'cid:' . uniqid();

        $this->_attachments[] = $embed;

        return $embed['ContentID'];
    }

    /**
     * @param array|string $emailsData email can be defined as string. In this case no transformation is done
     *                                 or as an array ['email@test.com', 'email2@test.com' => 'Email 2']
     * @return string|null
     */
    public static function stringifyEmails($emailsData)
    {
        $emails = null;
        if (empty($emailsData) === false) {
            if (is_array($emailsData) === true) {
                foreach ($emailsData as $key => $email) {
                    if (is_int($key) === true) {
                        $emails[] = $email;
                    } else {
                        if (preg_match('/[.,:]/', $email) > 0) {
                            $email = '"'. $email .'"';
                        }
                        $emails[] = $email . ' ' . '<' . $key . '>';
                    }
                }
                $emails = implode(', ', $emails);
            } elseif (is_string($emailsData) === true) {
                $emails = $emailsData;
            }
        }
        return $emails;
    }

    public static function convertEmails($emailsData)
    {
        $emails = [];
        if (empty($emailsData) === false) {
            if (is_array($emailsData) === true) {
                foreach ($emailsData as $key => $email) {
                    if (is_int($key) === true) {
                        $emails[] = [
                            'Email' => $email,
                        ];
                    } else {
                        /*if (preg_match('/[.,:]/', $email) > 0) {
                            $email = '"'. $email .'"';
                        }*/
                        $emails[] = [
                            'Email' => $key,
                            'Name' => $email,
                        ];
                    }
                }
            } elseif (is_string($emailsData) === true) {
                // "Test, Le" <email@plop.com>
                if (preg_match('/"([^"]+)"\s<([^>]+)>/', $emailsData, $matches) > 0) {
                    $emails[] = [
                        'Email' => $matches[2],
                        'Name' => $matches[1],
                    ];
                } else {
                    $emails[] = [
                        'Email' => $emailsData,
                    ];
                }
            }
        }
        return $emails;
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return join(',', $this->getTo()) . "\n"
        . $this->getSubject() . "\n"
        . $this->getTextBody();
    }

}
