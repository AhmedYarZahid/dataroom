<?php

namespace common\components\managers;

use Yii;
use yii\base\Component;
use backend\modules\contact\models\Contact;
use common\models\Newsletter;

class NewsletterManager extends Component
{
    /**
     * @param  Contact $contact
     * @return Newsletter
     */
    public function createFromContactForm(Contact $contact)
    {
        $nl = new Newsletter;

        $nl->email = $contact->email;
        $nl->firstName = $contact->firstName;
        $nl->lastName = $contact->lastName;
        $nl->userID = Yii::$app->user->id;

        $nl->save();

        return $nl;
    }
}