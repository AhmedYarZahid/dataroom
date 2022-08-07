<?php

use yii\db\Migration;

/**
 * Class m180517_074357_add_fields_to_ProfileCompany
 */
class m180517_074357_add_fields_to_ProfileCompany extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `ProfileCompany` ADD `geographicalArea` TINYINT  UNSIGNED  NULL  DEFAULT NULL  AFTER `entranceTicket`;
            ALTER TABLE `ProfileCompany` ADD `targetAmount` TINYINT  UNSIGNED  NULL  DEFAULT NULL  AFTER `geographicalArea`;
            ALTER TABLE `ProfileCompany` ADD `effective` VARCHAR(350)  NULL  DEFAULT NULL  AFTER `targetAmount`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180517_074357_add_fields_to_ProfileCompany cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180517_074357_add_fields_to_ProfileCompany cannot be reverted.\n";

        return false;
    }
    */
}
