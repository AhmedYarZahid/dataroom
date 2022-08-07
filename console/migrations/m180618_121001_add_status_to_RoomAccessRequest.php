<?php

use yii\db\Migration;

/**
 * Class m180618_121001_add_status_to_RoomAccessRequest
 */
class m180618_121001_add_status_to_RoomAccessRequest extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomAccessRequest` ADD `status` ENUM('waiting','accepted','refused')  NOT NULL  DEFAULT 'waiting'  AFTER `userID`;
            UPDATE `RoomAccessRequest` SET `status` = 'accepted' WHERE validatedBy IS NOT NULL;

            ALTER TABLE `RoomAccessRequest` ADD `refusedBy` INT(11)  NULL  DEFAULT NULL  AFTER `validatedBy`;
            ALTER TABLE `RoomAccessRequest` ADD CONSTRAINT `fk_RoomAccessRequest_User1` FOREIGN KEY (`refusedBy`) REFERENCES `User` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180618_121001_add_status_to_RoomAccessRequest cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180618_121001_add_status_to_RoomAccessRequest cannot be reverted.\n";

        return false;
    }
    */
}
