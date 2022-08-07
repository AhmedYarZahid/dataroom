<?php

use yii\db\Migration;

class m171130_082538_update_document_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE Contact
            DROP FOREIGN KEY fk_Contact_Document,
            DROP documentID;

            AlTER TABLE Document
            CHANGE type type enum('regular','contact','resume','cover_letter') NOT NULL DEFAULT 'regular',
            ADD contactID int(11) AFTER rank,
            ADD CONSTRAINT fk_Document_Contact FOREIGN KEY (contactID) REFERENCES Contact (id) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function safeDown()
    {
        echo "m171130_082538_update_document_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171130_082538_update_document_table cannot be reverted.\n";

        return false;
    }
    */
}
