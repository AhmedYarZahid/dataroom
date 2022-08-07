<?php

use yii\db\Migration;

class m180219_133807_update_User_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE User
            ADD profession varchar(70) NOT NULL DEFAULT '' AFTER type,
            ADD activity varchar(70) NOT NULL DEFAULT '' AFTER companyName,
            ADD phone varchar(30) NOT NULL DEFAULT '' AFTER lastName,
            CHANGE type type enum('user','manager','admin','superadmin') NOT NULL;
        ");
    }

    public function safeDown()
    {
        echo "m180219_133807_update_User_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180219_133807_update_User_table cannot be reverted.\n";

        return false;
    }
    */
}
