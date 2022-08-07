<?php

namespace backend\modules\contact\interfaces;

use backend\modules\contact\models\Contact;
use backend\modules\contact\models\ContactThread;

/**
 * @author Perica Levatic <perica.levatic@gmail.com>
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
interface ContactNotifyInterface
{
    /**
     * Sends new thread creation notification email to admin
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param Contact $contact
     * @return boolean
     */
    public static function sendNewContactMessage(Contact $contact);


    /**
     * Sends new user thread reply to admin
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param ContactThread $userReplyThread
     * @return boolean
     */
    public static function sendNewUserReply(ContactThread $userReplyThread);


    /**
     * Sends new admin thread reply to user
     * 
     * @author Perica Levatic <perica.levatic@gmail.com>
     *
     * @param ContactThread $adminReplyThread
     * @return boolean
     */
    public static function sendNewAdminReply(ContactThread $adminReplyThread);
}

?>
