<?php

use yii\db\Migration;

class m180316_115136_update_RoomCompany_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            UPDATE RoomCompany
            SET refNumber1 = NULL, refNumber2 = NULL;
            
            ALTER TABLE RoomCompany
            CHANGE refNumber1 refNumber1 date NULL,
            CHANGE refNumber2 refNumber2 date NULL;
        ");
    }

    public function safeDown()
    {
        echo "m180316_115136_update_RoomCompany_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180316_115136_update_RoomCompany_table cannot be reverted.\n";

        return false;
    }
    */
}
