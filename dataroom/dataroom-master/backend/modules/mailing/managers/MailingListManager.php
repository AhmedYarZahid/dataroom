<?php

namespace backend\modules\mailing\managers;

use backend\modules\mailing\models\MailingList;
use backend\modules\mailing\models\MailingContact;

class MailingListManager
{
    const TYPE_USER = 'user';
    const TYPE_NEWSLETTER = 'newsletter';

    /**
     * Creates a new mailing list.
     * 
     * @param  MailingList $list
     * @return bool Whether the list model was saved.
     */
    public function create(MailingList $list)
    {
        if ($list->validate() && $list->save()) {
            $this->syncContacts($list);
            return true;
        }

        return false;
    }

    /**
     * Updates a mailing list.
     * 
     * @param  MailingList $list
     * @return bool Whether the list model was saved.
     */
    public function update(MailingList $list)
    {
        if ($list->validate() && $list->save()) {
            $this->syncContacts($list);
            return true;
        }

        return false;
    }

    /**
     * Syncs contacts.
     * 
     * @param  MailingList $list
     */
    protected function syncContacts(MailingList $list)
    {
        $newContacts = $list->contactIds;
        $oldContacts = $list->loadContacts();
        $deleteContacts = array_diff($oldContacts, $newContacts);

        foreach ($deleteContacts as $contactId) {
            $this->deleteContact($contactId, $list->id);
        }

        foreach ($newContacts as $contactId) {
            if (in_array($contactId, $oldContacts)) {
                continue;
            }

            $this->createContact($contactId, $list->id);
        }
    }

    protected function createContact($contactId, $listId)
    {
        $contact = new MailingContact;
        $contact->listID = $listId;

        $type = $this->getContactType($contactId);
        $entityId = $this->getContactEntityId($contactId);

        switch ($type) {
            case self::TYPE_USER:
                $contact->userID = $entityId;
                break;
            
            case self::TYPE_NEWSLETTER:
                $contact->newsletterID = $entityId;
                break;
        }

        $contact->code = \Yii::$app->security->generateRandomString(32);

        return $contact->save();
    }

    protected function deleteContact($contactId, $listId)
    {
        $type = $this->getContactType($contactId);
        $entityId = $this->getContactEntityId($contactId);

        $query = MailingContact::find()->where(['listID' => $listId]);

        switch ($type) {
            case self::TYPE_USER:
                $query->andWhere(['userID' => $entityId]);
                break;
            
            case self::TYPE_NEWSLETTER:
                $query->andWhere(['newsletterID' => $entityId]);
                break;

            case self::TYPE_NEWSLETTER:
                $query->andWhere('0=1');
                break;
        }

        $contactModel = $query->one();
        if ($contactModel) {
            $contactModel->delete();
        }
    }

    protected function getContactType($contactId)
    {
        if (strpos($contactId, 'user_') !== false) {
            return self::TYPE_USER;
        } elseif (strpos($contactId, 'newsletter_') !== false) {
            return self::TYPE_NEWSLETTER;
        }
    }

    protected function getContactEntityId($contactId)
    {
        return str_replace(['user_', 'newsletter_'], '', $contactId);
    }
}