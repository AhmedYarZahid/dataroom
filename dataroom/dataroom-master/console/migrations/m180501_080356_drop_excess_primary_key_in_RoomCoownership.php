<?php

use yii\db\Migration;

/**
 * Class m180501_080356_drop_excess_primary_key_in_RoomCoownership
 */
class m180501_080356_drop_excess_primary_key_in_RoomCoownership extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP TABLE RoomCoownership;

            CREATE TABLE IF NOT EXISTS `RoomCoownership` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `roomID` INT NOT NULL,
              `propertyType` TINYINT(4) UNSIGNED NOT NULL,
              `address` VARCHAR(150) NOT NULL,
              `zip` VARCHAR(5) NOT NULL,
              `city` VARCHAR(70) NOT NULL,
              `regionID` SMALLINT NOT NULL,
              `latitude` DECIMAL(10,8) NULL DEFAULT NULL,
              `longitude` DECIMAL(11,8) NULL DEFAULT NULL,
              `missionEndDate` DATETIME NULL DEFAULT NULL,
              `coownershipName` VARCHAR(70) NOT NULL,
              `lotsNumber` INT NOT NULL,
              `coownersNumber` INT NULL DEFAULT NULL,
              `mainLotsNumber` INT NULL DEFAULT NULL,
              `secondaryLotsNumber` INT NULL DEFAULT NULL,
              `employeesNumber` INT NOT NULL,
              `lastFinancialYearApprovedAccountsID` INT NULL DEFAULT NULL,
              `constructionYear` SMALLINT NULL DEFAULT NULL,
              `totalFloorsNumber` SMALLINT NULL DEFAULT NULL,
              `isElevator` TINYINT(1) NULL DEFAULT NULL,
              `heatingType` ENUM('collective','individual') NULL DEFAULT NULL,
              `heatingEnergy` TINYINT UNSIGNED NULL DEFAULT NULL,
              `quickDescription` TEXT NULL DEFAULT NULL,
              `detailedDescription` TEXT NULL DEFAULT NULL,
              `keywords` TEXT NULL DEFAULT NULL,
              `procedure` TINYINT(4) UNSIGNED NOT NULL,
              `procedureContact` TEXT NOT NULL,
              `firstName` VARCHAR(70) NULL DEFAULT NULL,
              `lastName` VARCHAR(70) NULL DEFAULT NULL,
              `phone` VARCHAR(20) NULL DEFAULT NULL,
              `fax` VARCHAR(20) NULL DEFAULT NULL,
              `phoneMobile` VARCHAR(20) NULL DEFAULT NULL,
              `email` VARCHAR(150) NOT NULL,
              `adminContactEmail` VARCHAR(150) NOT NULL,
              `availabilityDate` DATE NOT NULL,
              `homePresence` TINYINT(1) NULL DEFAULT NULL,
              `visibility` TINYINT(1) NULL DEFAULT NULL,
              `offerAcceptanceCondition` TEXT NULL DEFAULT NULL,
              `individualAssetsPresence` TINYINT(1) NULL DEFAULT NULL,
              `presenceEndDate` DATE NULL DEFAULT NULL,
              `adPosition` SMALLINT NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_RoomCoownership_Room1_idx` (`roomID` ASC),
              INDEX `fk_RoomCoownership_Document1_idx` (`lastFinancialYearApprovedAccountsID` ASC),
              INDEX `fk_RoomCoownership_Region1_idx` (`regionID` ASC),
              CONSTRAINT `fk_RoomCoownership_Room1`
                FOREIGN KEY (`roomID`)
                REFERENCES `Room` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCoownership_Document1`
                FOREIGN KEY (`lastFinancialYearApprovedAccountsID`)
                REFERENCES `Document` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCoownership_Region1`
                FOREIGN KEY (`regionID`)
                REFERENCES `Region` (`id`)
                ON DELETE RESTRICT
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
        echo "m180501_080356_drop_excess_primary_key_in_RoomCoownership cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180501_080356_drop_excess_primary_key_in_RoomCoownership cannot be reverted.\n";

        return false;
    }
    */
}
