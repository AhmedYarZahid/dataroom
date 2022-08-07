<?php

use yii\db\Migration;

/**
 * Class m180418_141029_add_ProfileRealEstate_tables
 */
class m180418_141029_add_ProfileRealEstate_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `ProfileRealEstate` (
              `userID` INT NOT NULL,
              `targetSector` TINYINT UNSIGNED NOT NULL,
              `targetedAssetsAmount` TINYINT UNSIGNED NOT NULL,
              `assetsDestination` TINYINT UNSIGNED NOT NULL,
              `operationNature` ENUM('sale','rent') NOT NULL,
              PRIMARY KEY (`userID`),
              CONSTRAINT `fk_ProfileRealEstate_User1`
                FOREIGN KEY (`userID`)
                REFERENCES `User` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `ProfileRealEstate2Region` (
              `profileRealEstateID` INT NOT NULL,
              `regionID` SMALLINT NOT NULL,
              PRIMARY KEY (`profileRealEstateID`, `regionID`),
              INDEX `fk_ProfileRealEstate2Region_Region1_idx` (`regionID` ASC),
              CONSTRAINT `fk_ProfileRealEstate2Region_ProfileRealEstate1`
                FOREIGN KEY (`profileRealEstateID`)
                REFERENCES `ProfileRealEstate` (`userID`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProfileRealEstate2Region_Region1`
                FOREIGN KEY (`regionID`)
                REFERENCES `Region` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180418_141029_add_ProfileRealEstate_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180418_141029_add_ProfileRealEstate_tables cannot be reverted.\n";

        return false;
    }
    */
}
