<?php

use yii\db\Migration;

class m180329_123417_add_DocumentHistory_table extends Migration
{
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE `DocumentHistory` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `documentID` int(11) NULL,
              `roomID` int(11) NULL,
              `userID` int(11) NULL,
              `ip` int(11) UNSIGNED NOT NULL,
              `createdDate` datetime NOT NULL,
              FOREIGN KEY (`documentID`) REFERENCES `Document` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`roomID`) REFERENCES `Room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`userID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );

            ALTER TABLE `Document`
            ADD downloads int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER contactID;
        ');
    }

    public function safeDown()
    {
        echo "m180329_123417_add_DocumentHistory_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180329_123417_add_DocumentHistory_table cannot be reverted.\n";

        return false;
    }
    */
}
