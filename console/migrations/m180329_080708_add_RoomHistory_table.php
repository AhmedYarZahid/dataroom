<?php

use yii\db\Migration;

class m180329_080708_add_RoomHistory_table extends Migration
{
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE `RoomHistory` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `roomID` int(11) NOT NULL,
              `userID` int(11) NULL,
              `hasFullAccess` tinyint(1) NOT NULL DEFAULT 0,
              `ip` int(11) NOT NULL,
              `createdDate` datetime NOT NULL,
              FOREIGN KEY (`roomID`) REFERENCES `Room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              FOREIGN KEY (`userID`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );
        ');
    }

    public function safeDown()
    {
        echo "m180329_080708_add_RoomHistory_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180329_080708_add_RoomHistory_table cannot be reverted.\n";

        return false;
    }
    */
}
