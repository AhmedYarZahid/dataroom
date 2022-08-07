<?php

use yii\db\Migration;

class m180321_151241_update_Room_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE Room
            ADD proposalsAllowed tinyint(1) NOT NULL DEFAULT 0 AFTER section;
        ");
    }

    public function safeDown()
    {
        echo "m180321_151241_update_Room_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180321_151241_update_Room_table cannot be reverted.\n";

        return false;
    }
    */
}
