<?php

use yii\db\Migration;

/**
 * Class m180508_111216_add_roomID_to_MailingCampaign
 */
class m180508_111216_add_roomID_to_MailingCampaign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `MailingCampaign` ADD `roomID` INT  NULL  DEFAULT NULL  AFTER `userID`;
            ALTER TABLE `MailingCampaign` ADD CONSTRAINT `fk_MailingCampaign_Room1` FOREIGN KEY (`roomID`) REFERENCES `Room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180508_111216_add_roomID_to_MailingCampaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180508_111216_add_roomID_to_MailingCampaign cannot be reverted.\n";

        return false;
    }
    */
}
