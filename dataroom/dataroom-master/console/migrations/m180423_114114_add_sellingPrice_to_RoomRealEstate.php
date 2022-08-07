<?php

use yii\db\Migration;

/**
 * Class m180423_114114_add_sellingPrice_to_RoomRealEstate
 */
class m180423_114114_add_sellingPrice_to_RoomRealEstate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomRealEstate` ADD `sellingPrice` DECIMAL(10,2)  NULL  DEFAULT NULL  AFTER `keywords`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180423_114114_add_sellingPrice_to_RoomRealEstate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180423_114114_add_sellingPrice_to_RoomRealEstate cannot be reverted.\n";

        return false;
    }
    */
}
