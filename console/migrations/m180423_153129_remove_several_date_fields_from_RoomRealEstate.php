<?php

use yii\db\Migration;

/**
 * Class m180423_153129_remove_several_date_fields_from_RoomRealEstate
 */
class m180423_153129_remove_several_date_fields_from_RoomRealEstate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomRealEstate` DROP `openingDate`;
            ALTER TABLE `RoomRealEstate` DROP `closingDate`;
            ALTER TABLE `RoomRealEstate` DROP `tendersSubmissionDeadline`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180423_153129_remove_several_date_fields_from_RoomRealEstate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180423_153129_remove_several_date_fields_from_RoomRealEstate cannot be reverted.\n";

        return false;
    }
    */
}
