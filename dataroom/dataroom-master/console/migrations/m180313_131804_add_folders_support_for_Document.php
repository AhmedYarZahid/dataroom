<?php

use yii\db\Migration;

class m180313_131804_add_folders_support_for_Document extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `Document` ADD `isFolder` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `size`;
            ALTER TABLE `Document` ADD `parentID` INT  NULL  DEFAULT NULL  AFTER `title`;
            ALTER TABLE `Document` ADD CONSTRAINT `fk_Document_Document1` FOREIGN KEY (`parentID`) REFERENCES `Document` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function safeDown()
    {
        echo "m180313_131804_add_folders_support_for_Document cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180313_131804_add_folders_support_for_Document cannot be reverted.\n";

        return false;
    }
    */
}
