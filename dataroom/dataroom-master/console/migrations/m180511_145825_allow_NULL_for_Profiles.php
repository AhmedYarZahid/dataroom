<?php

use yii\db\Migration;

/**
 * Class m180511_145825_allow_NULL_for_Profiles
 */
class m180511_145825_allow_NULL_for_Profiles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `ProfileCoownership` CHANGE `lotsNumber` `lotsNumber` INT(11)  NULL;
            ALTER TABLE `ProfileCoownership` CHANGE `propertyType` `propertyType` TINYINT(4)  UNSIGNED  NULL;

            ALTER TABLE `ProfileRealEstate` CHANGE `targetSector` `targetSector` TINYINT(3)  UNSIGNED  NULL;
            ALTER TABLE `ProfileRealEstate` CHANGE `targetedAssetsAmount` `targetedAssetsAmount` TINYINT(3)  UNSIGNED  NULL;
            ALTER TABLE `ProfileRealEstate` CHANGE `assetsDestination` `assetsDestination` TINYINT(3)  UNSIGNED  NULL;
            ALTER TABLE `ProfileRealEstate` CHANGE `operationNature` `operationNature` ENUM('sale','rent')  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT NULL;
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180511_145825_allow_NULL_for_Profiles cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180511_145825_allow_NULL_for_Profiles cannot be reverted.\n";

        return false;
    }
    */
}
