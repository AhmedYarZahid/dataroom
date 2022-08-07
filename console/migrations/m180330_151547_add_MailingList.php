<?php

use yii\db\Migration;

class m180330_151547_add_MailingList extends Migration
{
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `MailingList` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `newsletterID` INT NOT NULL,
              `name` VARCHAR(45) NOT NULL,
              `createdByUserID` INT NOT NULL,
              `createdDate` DATETIME NOT NULL,
              `updatedDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `fk_MailingList_Newsletter1_idx` (`newsletterID` ASC),
              INDEX `fk_MailingList_User1_idx` (`createdByUserID` ASC),
              CONSTRAINT `fk_MailingList_Newsletter1`
                FOREIGN KEY (`newsletterID`)
                REFERENCES `Newsletter` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_MailingList_User1`
                FOREIGN KEY (`createdByUserID`)
                REFERENCES `User` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci
        ");
    }

    public function safeDown()
    {
        echo "m180330_151547_add_MailingList cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180330_151547_add_MailingList cannot be reverted.\n";

        return false;
    }
    */
}
