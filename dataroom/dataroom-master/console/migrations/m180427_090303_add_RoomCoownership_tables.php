<?php

use yii\db\Migration;

/**
 * Class m180427_090303_add_Coownership_tables
 */
class m180427_090303_add_RoomCoownership_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
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
              PRIMARY KEY (`id`, `roomID`),
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
            COLLATE = utf8_unicode_ci;


            CREATE TABLE IF NOT EXISTS `RoomAccessRequestCoownership` (
              `accessRequestID` INT(11) NOT NULL,
              `personType` ENUM('physical','legal') NOT NULL,
              `candidatePresentation` TEXT NULL DEFAULT NULL,
              `identityCardID` INT NULL DEFAULT NULL,
              `cvID` INT NULL DEFAULT NULL,
              `lastTaxDeclarationID` INT NULL DEFAULT NULL,
              `coownershipManagementReferenceID` INT NULL DEFAULT NULL,
              `groupPresentation` TEXT NULL DEFAULT NULL,
              `kbisID` INT NULL DEFAULT NULL,
              `latestCertifiedAccountsID` INT NULL DEFAULT NULL,
              `capitalAllocationID` INT NULL DEFAULT NULL,
              INDEX `accessRequestID` (`accessRequestID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document1_idx` (`identityCardID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document2_idx` (`cvID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document3_idx` (`lastTaxDeclarationID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document4_idx` (`kbisID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document5_idx` (`coownershipManagementReferenceID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document6_idx` (`latestCertifiedAccountsID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document7_idx` (`capitalAllocationID` ASC),
              PRIMARY KEY (`accessRequestID`),
              CONSTRAINT `roomaccessrequestCoownership_ibfk_1`
                FOREIGN KEY (`accessRequestID`)
                REFERENCES `RoomAccessRequest` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestCoownership_Document1`
                FOREIGN KEY (`identityCardID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestCoownership_Document2`
                FOREIGN KEY (`cvID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestCoownership_Document3`
                FOREIGN KEY (`lastTaxDeclarationID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestCoownership_Document4`
                FOREIGN KEY (`kbisID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestCoownership_Document5`
                FOREIGN KEY (`coownershipManagementReferenceID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestCoownership_Document6`
                FOREIGN KEY (`latestCertifiedAccountsID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestCoownership_Document7`
                FOREIGN KEY (`capitalAllocationID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;


            CREATE TABLE IF NOT EXISTS `ProposalCoownership` (
              `proposalID` INT(11) NOT NULL,
              `documentID` INT(11) NOT NULL,
              `companyName` VARCHAR(50) NOT NULL,
              `fullName` VARCHAR(100) NOT NULL,
              `address` VARCHAR(150) NOT NULL,
              `email` VARCHAR(150) NOT NULL,
              `phone` VARCHAR(20) NOT NULL,
              `kbisID` INT NOT NULL,
              `cniID` INT NOT NULL,
              `businessCardID` INT NOT NULL,
              INDEX `proposalID` (`proposalID` ASC),
              INDEX `fk_ProposalRealEstate_Document1_idx` (`documentID` ASC),
              INDEX `fk_ProposalRealEstate_Document2_idx` (`kbisID` ASC),
              INDEX `fk_ProposalRealEstate_Document3_idx` (`cniID` ASC),
              INDEX `fk_ProposalRealEstate_Document4_idx` (`businessCardID` ASC),
              PRIMARY KEY (`proposalID`),
              CONSTRAINT `proposalCoownership_ibfk_1`
                FOREIGN KEY (`proposalID`)
                REFERENCES `Proposal` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalCoownership_Document1`
                FOREIGN KEY (`documentID`)
                REFERENCES `Document` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalCoownership_Document2`
                FOREIGN KEY (`kbisID`)
                REFERENCES `Document` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalCoownership_Document3`
                FOREIGN KEY (`cniID`)
                REFERENCES `Document` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalCoownership_Document4`
                FOREIGN KEY (`businessCardID`)
                REFERENCES `Document` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180427_090303_add_Coownership_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180427_090303_add_Coownership_tables cannot be reverted.\n";

        return false;
    }
    */
}
