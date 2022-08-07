<?php

use yii\db\Migration;

/**
 * Class m180619_110341_add_refNumber0_to_RoomCompany
 */
class m180619_110341_add_refNumber0_to_RoomCompany extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomCompany` ADD `refNumber0` DATETIME  NULL  AFTER `hearingDate`;
            ALTER TABLE `RoomCompany` CHANGE `refNumber1` `refNumber1` DATETIME  NULL;
            ALTER TABLE `RoomCompany` CHANGE `refNumber2` `refNumber2` DATETIME  NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180619_110341_add_refNumber0_to_RoomCompany cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180619_110341_add_refNumber0_to_RoomCompany cannot be reverted.\n";

        return false;
    }
    */
}
