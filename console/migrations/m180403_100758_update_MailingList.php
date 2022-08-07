<?php

use yii\db\Migration;

class m180403_100758_update_MailingList extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE MailingList DROP FOREIGN KEY fk_MailingList_Newsletter1;
            ALTER TABLE MailingList DROP COLUMN newsletterID;

            CREATE TABLE `MailingContact` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `listID` int(11) NOT NULL,
              `userID` int(11) NULL,
              `newsletterID` int(11) NULL,
              `createdDate` datetime NOT NULL,
              FOREIGN KEY (`listID`) REFERENCES `MailingList` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`userID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`newsletterID`) REFERENCES `Newsletter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );

            CREATE TABLE `MailingCampaign` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `listID` int(11) NOT NULL,
              `userID` int(11) NULL,
              `sender` varchar(255) NOT NULL,
              `subject` varchar(255) NOT NULL,
              `body` text NOT NULL,
              `status` enum('draft','sent') NOT NULL DEFAULT 'draft',
              `createdDate` datetime NOT NULL,
              `updatedDate` datetime NOT NULL,
              FOREIGN KEY (`listID`) REFERENCES `MailingList` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`userID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );
        ");
    }

    public function safeDown()
    {
        echo "m180403_100758_update_MailingList cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180403_100758_update_MailingList cannot be reverted.\n";

        return false;
    }
    */
}
