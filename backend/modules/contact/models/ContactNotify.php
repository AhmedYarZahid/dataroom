<?php

namespace backend\modules\contact\models;

use Yii;
use backend\modules\contact\interfaces\ContactNotifyInterface;
use backend\modules\contact\models\Contact;
use backend\modules\notify\models\Notify;

/**
 * @author Perica Levatic <perica.levatic@gmail.com>
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class ContactNotify implements ContactNotifyInterface
{

    /**
     * Sends new thread creation notification email to admin
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param Contact $contact
     * @return boolean
     */
    public static function sendNewContactMessage(Contact $contact)
    {
        return Notify::sendContactUs($contact);
    }

    /**
     * Sends user reply to admin
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param ContactThread $userReplyThread
     * @return boolean
     */
    public static function sendNewUserReply(ContactThread $userReplyThread)
    {
        return Notify::sendContactUsUserReply($userReplyThread);
    }

    /**
     * Sends admin reply to user
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param ContactThread $adminReplyThread
     * @return boolean
     */
    public static function sendNewAdminReply(ContactThread $adminReplyThread)
    {
        return Notify::sendContactUsAdminReply($adminReplyThread);
    }
}

?>
