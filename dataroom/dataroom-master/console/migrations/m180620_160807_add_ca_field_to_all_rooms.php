<?php

use yii\db\Migration;

/**
 * Class m180620_160807_add_ca_field_to_all_rooms
 */
class m180620_160807_add_ca_field_to_all_rooms extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomCoownership` ADD `ca` INT  NULL  DEFAULT NULL  AFTER `roomID`;
            ALTER TABLE `RoomCV` ADD `ca` INT  NULL  DEFAULT NULL  AFTER `roomID`;
            ALTER TABLE `RoomRealEstate` ADD `ca` INT  NULL  DEFAULT NULL  AFTER `roomID`;

            ALTER TABLE `RoomAccessRequestCoownership` ADD `agreementID` INT  NULL  DEFAULT NULL  AFTER `accessRequestID`;
            ALTER TABLE `RoomAccessRequestCoownership` ADD CONSTRAINT `fk_RoomAccessRequestCoownership_Document8` FOREIGN KEY (`agreementID`) REFERENCES `Document` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
            ALTER TABLE `RoomAccessRequestRealEstate` ADD `agreementID` INT  NULL  DEFAULT NULL  AFTER `accessRequestID`;
            ALTER TABLE `RoomAccessRequestRealEstate` ADD CONSTRAINT `fk_RoomAccessRequestRealEstate_Document8` FOREIGN KEY (`agreementID`) REFERENCES `Document` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180620_160807_add_ca_field_to_all_rooms cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180620_160807_add_ca_field_to_all_rooms cannot be reverted.\n";

        return false;
    }
    */
}
