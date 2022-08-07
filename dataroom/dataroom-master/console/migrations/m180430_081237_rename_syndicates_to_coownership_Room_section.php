<?php

use yii\db\Migration;

/**
 * Class m180430_081237_rename_syndicates_to_coownership_Room_section
 */
class m180430_081237_rename_syndicates_to_coownership_Room_section extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `Room` CHANGE `section` `section` ENUM('companies','real_estate','coownership','cv')
             CHARACTER SET utf8mb4
             COLLATE utf8mb4_general_ci
             NOT NULL;

             ALTER TABLE `Room` CHARACTER SET = utf8;
             ALTER TABLE `Room` COLLATE = utf8_unicode_ci;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180430_081237_rename_syndicates_to_coownership_Room_section cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180430_081237_rename_syndicates_to_coownership_Room_section cannot be reverted.\n";

        return false;
    }
    */
}
