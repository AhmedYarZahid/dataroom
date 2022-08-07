<?php

use yii\db\Migration;

/**
 * Class m180611_170317_change_effective_type_in_ProfileCompany
 */
class m180611_170317_change_effective_type_in_ProfileCompany extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            UPDATE ProfileCompany SET effective = '0' WHERE effective IS NOT NULL;
            ALTER TABLE `ProfileCompany` CHANGE `effective` `effective` INT(11)  NULL  DEFAULT NULL;
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180611_170317_change_effective_type_in_ProfileCompany cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180611_170317_change_effective_type_in_ProfileCompany cannot be reverted.\n";

        return false;
    }
    */
}
