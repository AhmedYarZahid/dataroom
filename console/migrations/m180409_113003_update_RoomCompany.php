<?php

use yii\db\Migration;

/**
 * Class m180409_113003_update_RoomCompany
 */
class m180409_113003_update_RoomCompany extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE RoomCompany
            CHANGE codeNaf codeNaf varchar(10) NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_113003_update_RoomCompany cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180409_113003_update_RoomCompany cannot be reverted.\n";

        return false;
    }
    */
}
