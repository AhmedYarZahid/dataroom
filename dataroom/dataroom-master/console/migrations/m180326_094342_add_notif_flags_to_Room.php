<?php

use yii\db\Migration;

class m180326_094342_add_notif_flags_to_Room extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE Room
            ADD publicationAlertSent tinyint(1) NOT NULL DEFAULT 0 AFTER archivationDate,
            ADD expirationAlertSent tinyint(1) NOT NULL DEFAULT 0 AFTER publicationAlertSent,
            ADD pendingUpdateAlert tinyint(1) NOT NULL DEFAULT 0 AFTER expirationAlertSent;

            ALTER TABLE RoomCompany
            ADD hearingAlertSent tinyint(1) NOT NULL DEFAULT 0;
        ");
    }

    public function safeDown()
    {
        echo "m180326_094342_add_notif_flags_to_Room cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180326_094342_add_notif_flags_to_Room cannot be reverted.\n";

        return false;
    }
    */
}
