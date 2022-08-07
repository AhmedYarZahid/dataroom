<?php

use yii\db\Migration;

/**
 * Class m180619_123132_add_adminID_to_Room
 */
class m180619_123132_add_adminID_to_Room extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `Room` ADD `adminID` INT(11)  NOT NULL  AFTER `userID`;
            UPDATE Room SET adminID = (SELECT id FROM User WHERE type = 'admin' LIMIT 1);
            ALTER TABLE `Room` ADD CONSTRAINT `fk_Room_User1` FOREIGN KEY (`adminID`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

            ALTER TABLE `RoomCompany` DROP `studyContact`;
            ALTER TABLE `RoomRealEstate` DROP `adminContactEmail`;
            ALTER TABLE `RoomCoownership` DROP `adminContactEmail`;

            UPDATE Notify SET putToQueue = 1 WHERE eventID = 23;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180619_123132_add_adminID_to_Room cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180619_123132_add_adminID_to_Room cannot be reverted.\n";

        return false;
    }
    */
}
