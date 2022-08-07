<?php

use yii\db\Migration;

/**
 * Class m180501_084735_alter_document_constraint_in_RoomCoownership
 */
class m180501_084735_alter_document_constraint_in_RoomCoownership extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomCoownership` DROP FOREIGN KEY `fk_RoomCoownership_Document1`;
            ALTER TABLE `RoomCoownership` ADD CONSTRAINT `fk_RoomCoownership_Document1` FOREIGN KEY (`lastFinancialYearApprovedAccountsID`) REFERENCES `Document` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180501_084735_alter_document_constraint_in_RoomCoownership cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180501_084735_alter_document_constraint_in_RoomCoownership cannot be reverted.\n";

        return false;
    }
    */
}
