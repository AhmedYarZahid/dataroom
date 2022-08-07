<?php

use yii\db\Migration;

class m180329_120822_add_unsigned_to_ip_fields extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE UserHistory
            CHANGE ip ip int(11) UNSIGNED NOT NULL;

            ALTER TABLE RoomHistory
            CHANGE ip ip int(11) UNSIGNED NOT NULL;
        ");
    }

    public function safeDown()
    {
        echo "m180329_120822_add_unsigned_to_ip_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180329_120822_add_unsigned_to_ip_fields cannot be reverted.\n";

        return false;
    }
    */
}
