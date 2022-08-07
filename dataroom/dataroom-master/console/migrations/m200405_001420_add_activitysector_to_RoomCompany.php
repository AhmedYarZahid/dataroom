<?php

use yii\db\Migration;

/**
 * Class m200405_001420_add_activitysector_to_RoomCompany
 */
class m200405_001420_add_activitysector_to_RoomCompany extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("

            ALTER TABLE RoomCompany

            ADD activitysector varchar(50) NULL AFTER activity;

        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200405_001420_add_activitysector_to_RoomCompany cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200405_001420_add_activitysector_to_RoomCompany cannot be reverted.\n";

        return false;
    }
    */
}
