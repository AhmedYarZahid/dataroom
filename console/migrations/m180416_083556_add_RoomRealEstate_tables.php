<?php

use yii\db\Migration;

/**
 * Class m180416_083556_add_RoomRealEstate_tables
 */
class m180416_083556_add_RoomRealEstate_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `Country` (
              `id` SMALLINT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(50) NOT NULL,
              `code` CHAR(2) NOT NULL,
              `isDefault` TINYINT(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `Region` (
              `id` SMALLINT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(50) NOT NULL,
              `code` TINYINT(2) UNSIGNED NOT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS `RoomRealEstate` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `roomID` INT(11) NOT NULL,
              `mission` VARCHAR(250) NOT NULL,
              `marketing` ENUM('sale','rent') NULL DEFAULT NULL,
              `status` ENUM('free','occupied') NULL DEFAULT NULL,
              `propertyType` TINYINT(4) UNSIGNED NOT NULL,
              `propertySubType` TINYINT(4) UNSIGNED NOT NULL,
              `libAd` VARCHAR(250) NOT NULL,
              `address` VARCHAR(150) NULL DEFAULT '',
              `zip` VARCHAR(5) NOT NULL,
              `city` VARCHAR(70) NOT NULL,
              `countryID` SMALLINT NOT NULL,
              `regionID` SMALLINT NOT NULL,
              `latitude` DECIMAL(10,8) NULL DEFAULT NULL,
              `longitude` DECIMAL(11,8) NULL DEFAULT NULL,
              `constructionYear` SMALLINT NULL DEFAULT NULL,
              `totalFloorsNumber` SMALLINT NULL DEFAULT NULL,
              `floorNumber` SMALLINT NULL DEFAULT NULL,
              `area` FLOAT NOT NULL,
              `isDuplex` TINYINT(1) NULL DEFAULT NULL,
              `isElevator` TINYINT(1) NULL DEFAULT NULL,
              `roomsNumber` TINYINT UNSIGNED NULL DEFAULT NULL,
              `bedroomsNumber` TINYINT UNSIGNED NULL DEFAULT NULL,
              `bathroomsNumber` TINYINT UNSIGNED NULL DEFAULT NULL,
              `showerRoomsNumber` TINYINT UNSIGNED NULL DEFAULT NULL,
              `kitchensNumber` TINYINT UNSIGNED NULL DEFAULT NULL,
              `toiletsNumber` TINYINT UNSIGNED NULL DEFAULT NULL,
              `isSeparateToilet` TINYINT(1) NULL DEFAULT NULL,
              `separateToiletsNumber` TINYINT UNSIGNED NULL DEFAULT NULL,
              `heatingType` ENUM('collective','individual') NULL DEFAULT NULL,
              `heatingEnergy` TINYINT UNSIGNED NULL DEFAULT NULL,
              `proximity` VARCHAR(250) NULL DEFAULT '',
              `quickDescription` TEXT NULL DEFAULT NULL,
              `detailedDescription` TEXT NULL DEFAULT NULL,
              `keywords` TEXT NULL DEFAULT NULL,
              `totalPrice` DECIMAL(10,2) NULL DEFAULT NULL,
              `totalPriceFrequency` TINYINT(4) UNSIGNED NULL DEFAULT NULL,
              `charges` DECIMAL(10,2) NULL DEFAULT NULL,
              `chargesFrequency` TINYINT(4) UNSIGNED NULL DEFAULT NULL,
              `currency` ENUM('eur','usd') NULL DEFAULT NULL,
              `propertyTax` DECIMAL(10,2) NULL DEFAULT NULL,
              `housingTax` DECIMAL(10,2) NULL DEFAULT NULL,
              `condominiumLotsNumber` SMALLINT NULL DEFAULT NULL,
              `adLotNumber` SMALLINT NULL DEFAULT NULL,
              `procedure` TINYINT(4) UNSIGNED NOT NULL,
              `procedureContact` TEXT NOT NULL,
              `firstName` VARCHAR(70) NULL DEFAULT NULL,
              `lastName` VARCHAR(70) NULL DEFAULT NULL,
              `phone` VARCHAR(20) NULL DEFAULT NULL,
              `fax` VARCHAR(20) NULL DEFAULT NULL,
              `phoneMobile` VARCHAR(20) NULL DEFAULT NULL,
              `email` VARCHAR(150) NOT NULL,
              `adminContactEmail` VARCHAR(150) NOT NULL,
              `openingDate` DATE NOT NULL,
              `closingDate` DATE NOT NULL,
              `tendersSubmissionDeadline` DATE NOT NULL,
              `availabilityDate` DATE NOT NULL,
              `homePresence` TINYINT(1) NULL DEFAULT NULL,
              `visibility` TINYINT(1) NULL DEFAULT NULL,
              `offerAcceptanceCondition` TEXT NULL DEFAULT NULL,
              `individualAssetsPresence` TINYINT(1) NULL DEFAULT NULL,
              `presenceEndDate` DATE NULL DEFAULT NULL,
              `adPosition` SMALLINT NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              INDEX `roomID` (`roomID` ASC),
              INDEX `fk_RoomRealEstate_Region1_idx` (`regionID` ASC),
              INDEX `fk_RoomRealEstate_Country1_idx` (`countryID` ASC),
              CONSTRAINT `roomrealestate_ibfk_1`
                FOREIGN KEY (`roomID`)
                REFERENCES `Room` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomRealEstate_Region1`
                FOREIGN KEY (`regionID`)
                REFERENCES `Region` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomRealEstate_Country1`
                FOREIGN KEY (`countryID`)
                REFERENCES `Country` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            AUTO_INCREMENT = 8
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS `RoomFacility` (
              `id` SMALLINT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(70) NOT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `RoomCupboard` (
              `id` SMALLINT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(70) NOT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `RoomType` (
              `id` SMALLINT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(70) NOT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `RoomOrientation` (
              `id` SMALLINT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(70) NOT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;
         ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS `RoomRealEstate2Facility` (
              `roomRealEstateID` INT NOT NULL,
              `facilityID` SMALLINT NOT NULL,
              PRIMARY KEY (`roomRealEstateID`, `facilityID`),
              INDEX `fk_RoomRealEstate2Facility_RoomFacility1_idx` (`facilityID` ASC),
              CONSTRAINT `fk_RoomRealEstate2Facility_RoomRealEstate1`
                FOREIGN KEY (`roomRealEstateID`)
                REFERENCES `RoomRealEstate` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomRealEstate2Facility_RoomFacility1`
                FOREIGN KEY (`facilityID`)
                REFERENCES `RoomFacility` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `RoomRealEstate2Cupboard` (
              `roomRealEstateID` INT NOT NULL,
              `cupboardID` SMALLINT NOT NULL,
              PRIMARY KEY (`roomRealEstateID`, `cupboardID`),
              INDEX `fk_RoomRealEstate2Cupboard_RoomCupboard1_idx` (`cupboardID` ASC),
              CONSTRAINT `fk_RoomRealEstate2Cupboard_RoomRealEstate1`
                FOREIGN KEY (`roomRealEstateID`)
                REFERENCES `RoomRealEstate` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomRealEstate2Cupboard_RoomCupboard1`
                FOREIGN KEY (`cupboardID`)
                REFERENCES `RoomCupboard` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `RoomRealEstate2RoomType` (
              `roomRealEstateID` INT NOT NULL,
              `roomTypeID` SMALLINT NOT NULL,
              PRIMARY KEY (`roomRealEstateID`, `roomTypeID`),
              INDEX `fk_RoomRealEstate2RoomType_RoomType1_idx` (`roomTypeID` ASC),
              CONSTRAINT `fk_RoomRealEstate2RoomType_RoomRealEstate1`
                FOREIGN KEY (`roomRealEstateID`)
                REFERENCES `RoomRealEstate` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomRealEstate2RoomType_RoomType1`
                FOREIGN KEY (`roomTypeID`)
                REFERENCES `RoomType` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `RoomRealEstate2Orientation` (
              `roomRealEstateID` INT NOT NULL,
              `orientationID` SMALLINT NOT NULL,
              PRIMARY KEY (`roomRealEstateID`, `orientationID`),
              INDEX `fk_RoomRealEstate2Orientation_RoomOrientation1_idx` (`orientationID` ASC),
              CONSTRAINT `fk_RoomRealEstate2Orientation_RoomRealEstate1`
                FOREIGN KEY (`roomRealEstateID`)
                REFERENCES `RoomRealEstate` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomRealEstate2Orientation_RoomOrientation1`
                FOREIGN KEY (`orientationID`)
                REFERENCES `RoomOrientation` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS `RoomAccessRequestRealEstate` (
              `accessRequestID` INT(11) NOT NULL,
              `personType` ENUM('physical','legal') NOT NULL,
              `candidatePresentation` TEXT NULL DEFAULT NULL,
              `identityCardID` INT NULL DEFAULT NULL,
              `cvID` INT NULL DEFAULT NULL,
              `lastTaxDeclarationID` INT NULL DEFAULT NULL,
              `companyPresentation` TEXT NULL DEFAULT NULL,
              `kbisID` INT NULL DEFAULT NULL,
              `registrationsUpdatedStatusID` INT NULL DEFAULT NULL,
              `latestCertifiedAccountsID` INT NULL DEFAULT NULL,
              `capitalAllocationID` INT NULL DEFAULT NULL,
              PRIMARY KEY (`accessRequestID`),
              INDEX `accessRequestID` (`accessRequestID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document1_idx` (`identityCardID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document2_idx` (`cvID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document3_idx` (`lastTaxDeclarationID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document4_idx` (`kbisID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document5_idx` (`registrationsUpdatedStatusID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document6_idx` (`latestCertifiedAccountsID` ASC),
              INDEX `fk_RoomAccessRequestRealEstate_Document7_idx` (`capitalAllocationID` ASC),
              CONSTRAINT `roomaccessrequestRealEstate_ibfk_1`
                FOREIGN KEY (`accessRequestID`)
                REFERENCES `RoomAccessRequest` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestRealEstate_Document1`
                FOREIGN KEY (`identityCardID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestRealEstate_Document2`
                FOREIGN KEY (`cvID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestRealEstate_Document3`
                FOREIGN KEY (`lastTaxDeclarationID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestRealEstate_Document4`
                FOREIGN KEY (`kbisID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestRealEstate_Document5`
                FOREIGN KEY (`registrationsUpdatedStatusID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestRealEstate_Document6`
                FOREIGN KEY (`latestCertifiedAccountsID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestRealEstate_Document7`
                FOREIGN KEY (`capitalAllocationID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS `ProposalRealEstate` (
              `proposalID` INT(11) NOT NULL,
              `documentID` INT(11) NULL DEFAULT NULL,
              `firstName` VARCHAR(50) NOT NULL,
              `lastName` VARCHAR(70) NOT NULL,
              `address` VARCHAR(150) NOT NULL,
              `email` VARCHAR(150) NOT NULL,
              `phone` VARCHAR(20) NOT NULL,
              `kbisID` INT NULL DEFAULT NULL,
              `cniID` INT NULL DEFAULT NULL,
              `balanceSheetID` INT NULL DEFAULT NULL,
              `taxNoticeID` INT NULL DEFAULT NULL,
              PRIMARY KEY (`proposalID`),
              INDEX `proposalID` (`proposalID` ASC),
              INDEX `fk_ProposalRealEstate_Document1_idx` (`documentID` ASC),
              INDEX `fk_ProposalRealEstate_Document2_idx` (`kbisID` ASC),
              INDEX `fk_ProposalRealEstate_Document3_idx` (`cniID` ASC),
              INDEX `fk_ProposalRealEstate_Document4_idx` (`balanceSheetID` ASC),
              INDEX `fk_ProposalRealEstate_Document5_idx` (`taxNoticeID` ASC),
              CONSTRAINT `proposalrealestate_ibfk_1`
                FOREIGN KEY (`proposalID`)
                REFERENCES `Proposal` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalRealEstate_Document1`
                FOREIGN KEY (`documentID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalRealEstate_Document2`
                FOREIGN KEY (`kbisID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalRealEstate_Document3`
                FOREIGN KEY (`cniID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalRealEstate_Document4`
                FOREIGN KEY (`balanceSheetID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProposalRealEstate_Document5`
                FOREIGN KEY (`taxNoticeID`)
                REFERENCES `Document` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci
        ");

        $this->execute("
            INSERT INTO Country (name, code, isDefault)
            VALUES
            ('Angleterre', 'GB', 0),
            ('Belgique', 'BE', 0),
            ('Espagne', 'ES', 0),
            ('France', 'FR', 1),
            ('Italie', 'IT', 0),
            ('Luxembourg', 'LU', 0),
            ('Maroc', 'MA', 0),
            ('Portugal', 'PT', 0),
            ('Suisse', 'CH', 0);

            INSERT INTO Region (name, code)
            VALUES
            ('Haut de France', 32),
            ('Bretagne', 53),
            ('Normandie', 28),
            ('Ile de France', 11),
            ('Grand Est', 44),
            ('Pays de La Loire', 52),
            ('Centre - Val de Loire', 24),
            ('Bourgogne - Franche - Comté', 27),
            ('Nouvelle Acquitaine', 75),
            ('Auvergne-Rhône-Alpes', 84),
            ('Occitanie', 76),
            ('Provence - Alpes - Côte d\'azur', 93),
            ('Corse', 94);

            INSERT INTO RoomFacility (name)
            VALUES
            ('Alarme'),
            ('Balcon(s)'),
            ('Box'),
            ('Cheminée'),
            ('Climatisation'),
            ('Parking'),
            ('Parquet'),
            ('Piscine'),
            ('Terrasses(s)');

            INSERT INTO RoomCupboard (name)
            VALUES
            ('Cave'),
            ('Grenier'),
            ('Placards');

            INSERT INTO RoomType (name)
            VALUES
            ('Buanderie'),
            ('Dressing'),
            ('Salle à manger séparée'),
            ('Salle d\'eau'),
            ('Salle de bain'),
            ('Séjour'),
            ('Toilettes séparées');

            INSERT INTO RoomOrientation (name)
            VALUES
            ('Belle vue'),
            ('Est'),
            ('Nord'),
            ('Ouest'),
            ('Sans vis-à-vis'),
            ('Sud');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180416_083556_add_RoomRealEstate_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180416_083556_add_RoomRealEstate_tables cannot be reverted.\n";

        return false;
    }
    */
}
