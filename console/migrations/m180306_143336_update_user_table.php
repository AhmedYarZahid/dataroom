<?php

use yii\db\Migration;

class m180306_143336_update_user_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE User
            CHANGE passwordHash passwordHash varchar(255) NULL, 
            ADD oneTimeLoginToken varchar(100) NULL AFTER passwordResetToken,
            ADD birthPlace varchar(150) NOT NULL DEFAULT '' AFTER phoneMobile;
        ");
    }

    public function safeDown()
    {
        echo "m180306_143336_update_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180306_143336_update_user_table cannot be reverted.\n";

        return false;
    }
    */
}
