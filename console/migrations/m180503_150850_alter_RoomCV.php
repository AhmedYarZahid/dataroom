<?php

use yii\db\Migration;

/**
 * Class m180503_150850_alter_RoomCV
 */
class m180503_150850_alter_RoomCV extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomCV` DROP FOREIGN KEY `fk_RoomCV_Document1`;
            ALTER TABLE `RoomCV` ADD CONSTRAINT `fk_RoomCV_Document1` FOREIGN KEY (`cvID`) REFERENCES `Document` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180503_150850_alter_RoomCV cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180503_150850_alter_RoomCV cannot be reverted.\n";

        return false;
    }
    */
}
