<?php

use yii\db\Migration;

class m171124_100313_update_contact_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE Contact
            ADD civility enum('sir','madam','master') AFTER type,
            ADD mandate varchar(255) DEFAULT '' AFTER company,
            ADD documentID int(11) AFTER body,
            ADD CONSTRAINT fk_Contact_Document FOREIGN KEY (documentID) REFERENCES Document (id) ON DELETE CASCADE ON UPDATE CASCADE;

            AlTER TABLE Document
            CHANGE type type enum('regular','contact') NOT NULL DEFAULT 'regular';
        ");
    }

    public function safeDown()
    {
        echo "m171124_100313_update_contact_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171124_100313_update_contact_table cannot be reverted.\n";

        return false;
    }
    */
}
