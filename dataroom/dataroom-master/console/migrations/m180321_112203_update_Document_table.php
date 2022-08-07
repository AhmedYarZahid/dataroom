<?php

use yii\db\Migration;

class m180321_112203_update_Document_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            AlTER TABLE Document
            CHANGE type type enum('regular','contact','resume','cover_letter','proposal','access_request','room','room_image') NOT NULL DEFAULT 'regular';
        ");
    }

    public function safeDown()
    {
        echo "m180321_112203_update_Document_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180321_112203_update_Document_table cannot be reverted.\n";

        return false;
    }
    */
}
