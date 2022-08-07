<?php

use yii\db\Migration;

class m180226_120508_update_dataroom_tables extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomCompany`
            ADD `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

            CREATE TABLE `RoomAccessRequestCompany` (
              `accessRequestID` int(11) NOT NULL,
              `presentation` text NOT NULL,
              `kbis` int(11) NOT NULL,
              `balanceSheet` int(11) NOT NULL,
              `cni` int(11) NOT NULL,
              `commitment` int(11) NOT NULL,
              FOREIGN KEY (`accessRequestID`) REFERENCES `RoomAccessRequest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );

            CREATE TABLE `ProposalCompany` (
              `proposalID` int(11) NOT NULL,
              FOREIGN KEY (`proposalID`) REFERENCES `Proposal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );

            CREATE TABLE `ProfileCompany` (
              `userID` int(11) NOT NULL,
              `targetedSector` varchar(255) NULL,
              `targetedTurnover` varchar(255) NULL,
              `entranceTicket` varchar(255) NULL,
              FOREIGN KEY (`userID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );

            AlTER TABLE Document
            CHANGE type type enum('regular','contact','resume','cover_letter', 'proposal', 'access_request', 'room') NOT NULL DEFAULT 'regular';
        ");
    }

    public function safeDown()
    {
        echo "m180226_120508_update_room_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180226_120508_update_room_tables cannot be reverted.\n";

        return false;
    }
    */
}
