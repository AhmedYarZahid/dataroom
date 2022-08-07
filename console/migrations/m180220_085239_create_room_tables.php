<?php

use yii\db\Migration;

class m180220_085239_create_room_tables extends Migration
{
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `Room` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `creatorID` int(11) NOT NULL,
              `userID` int(11) NOT NULL,
              `title` varchar(255) NOT NULL,
              `status` enum('draft','published','expired','archived') NOT NULL,
              `publicationDate` datetime NOT NULL,
              `expirationDate` datetime NOT NULL,
              `archivationDate` datetime NOT NULL,
              `createdDate` datetime NOT NULL,
              `updatedDate` timestamp NOT NULL,
              FOREIGN KEY (`creatorID`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
              FOREIGN KEY (`userID`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
            );

            CREATE TABLE `RoomAccessRequest` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `roomID` int(11) NOT NULL,
              `userID` int(11) NOT NULL,
              `validatedBy` int(11) NULL,
              `createdDate` datetime NOT NULL,
              `updatedDate` timestamp NOT NULL,
              FOREIGN KEY (`roomID`) REFERENCES `Room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`userID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`validatedBy`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
            );

            CREATE TABLE `RoomCompany` (
              `roomID` int(11) NOT NULL,
              `activity` varchar(255) NULL,
              `region` varchar(255) NULL,
              `website` varchar(255) NULL,
              `address` varchar(255) NULL,
              `zip` varchar(5) NULL,
              `city` varchar(255) NULL,
              `desc` text NULL,
              `desc2` text NULL,
              `siren` varchar(9) NULL,
              `codeNaf` varchar(5) NULL,
              `legalStatus` text NULL,
              FOREIGN KEY (`roomID`) REFERENCES `Room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );

            CREATE TABLE `Proposal` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `roomID` int(11) NOT NULL,
              `userID` int(11) NOT NULL,
              `createdDate` datetime NOT NULL,
              FOREIGN KEY (`roomID`) REFERENCES `Room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`userID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );
        ");
    }

    public function safeDown()
    {
        echo "m180220_085239_create_room_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180220_085239_create_room_tables cannot be reverted.\n";

        return false;
    }
    */
}
