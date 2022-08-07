<?php

use yii\db\Migration;

/**
 * Class m180508_131001_add_unique_code_to_MailingContact
 */
class m180508_131001_add_unique_code_to_MailingContact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `MailingContact` ADD `code` CHAR(32)  NOT NULL  DEFAULT ''  AFTER `newsletterID`;
            ALTER TABLE `Newsletter` ADD `isActive` TINYINT(1)  NOT NULL  DEFAULT 1  AFTER `languageID`;
        ");

        $contacts = \backend\modules\mailing\models\MailingContact::find()->all();
        foreach ($contacts as $contact) {
            $contact->code = Yii::$app->security->generateRandomString(32);
            $contact->save(false, ['code']);
        }

        $this->execute("
            ALTER TABLE `MailingContact` ADD UNIQUE INDEX `ui_MailingContact_code` (`code`);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180508_131001_add_unique_code_to_MailingContact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180508_131001_add_unique_code_to_MailingContact cannot be reverted.\n";

        return false;
    }
    */
}
