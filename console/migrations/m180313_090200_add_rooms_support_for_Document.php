<?php

use yii\db\Migration;

class m180313_090200_add_rooms_support_for_Document extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `Document` ADD `createdDate` DATETIME  NOT NULL  DEFAULT CURRENT_TIMESTAMP  AFTER `contactID`;
            ALTER TABLE `Document` ADD `roomID` INT  NULL  DEFAULT NULL  AFTER `rank`;
            ALTER TABLE `Document` ADD CONSTRAINT `fk_Document_Room` FOREIGN KEY (`roomID`) REFERENCES `Room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            ALTER TABLE `Document` ADD `size` INT  NULL  DEFAULT NULL  AFTER `rank`;
        ");
    }

    public function safeDown()
    {
        echo "m180313_090200_update_rooms_support_for_Document cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180313_090200_add_rooms_support_for_Document cannot be reverted.\n";

        return false;
    }
    */
}
