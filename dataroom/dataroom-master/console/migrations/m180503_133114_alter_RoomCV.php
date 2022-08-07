<?php

use yii\db\Migration;

/**
 * Class m180503_133114_alter_RoomCV
 */
class m180503_133114_alter_RoomCV extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP TABLE RoomCV;

            CREATE TABLE IF NOT EXISTS `RoomCV` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `roomID` INT NOT NULL,
              `companyName` VARCHAR(70) NULL DEFAULT NULL,
              `activityDomainID` SMALLINT NULL DEFAULT NULL,
              `candidateProfile` TEXT NULL DEFAULT NULL,
              `functionID` SMALLINT NULL DEFAULT NULL,
              `subFunctionID` SMALLINT NULL DEFAULT NULL,
              `firstName` VARCHAR(50) NULL DEFAULT NULL,
              `lastName` VARCHAR(50) NULL DEFAULT NULL,
              `address` VARCHAR(150) NULL DEFAULT NULL,
              `email` VARCHAR(150) NULL DEFAULT NULL,
              `phone` VARCHAR(20) NULL DEFAULT NULL,
              `cvID` INT NULL DEFAULT NULL,
              `departmentID` SMALLINT NULL DEFAULT NULL,
              `regionID` SMALLINT NULL DEFAULT NULL,
              `seniority` VARCHAR(150) NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_RoomCV_Room1_idx` (`roomID` ASC),
              INDEX `fk_RoomCV_Department1_idx` (`departmentID` ASC),
              INDEX `fk_RoomCV_Document1_idx` (`cvID` ASC),
              INDEX `fk_RoomCV_Region1_idx` (`regionID` ASC),
              INDEX `fk_RoomCV_CVActivityDomain1_idx` (`activityDomainID` ASC),
              INDEX `fk_RoomCV_CVFunction1_idx` (`functionID` ASC),
              INDEX `fk_RoomCV_CVFunction2_idx` (`subFunctionID` ASC),
              CONSTRAINT `fk_RoomCV_Room1`
                FOREIGN KEY (`roomID`)
                REFERENCES `Room` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_Department1`
                FOREIGN KEY (`departmentID`)
                REFERENCES `Department` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_Document1`
                FOREIGN KEY (`cvID`)
                REFERENCES `Document` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_Region1`
                FOREIGN KEY (`regionID`)
                REFERENCES `Region` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_CVActivityDomain1`
                FOREIGN KEY (`activityDomainID`)
                REFERENCES `CVActivityDomain` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_CVFunction1`
                FOREIGN KEY (`functionID`)
                REFERENCES `CVFunction` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_CVFunction2`
                FOREIGN KEY (`subFunctionID`)
                REFERENCES `CVFunction` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE)
            ENGINE = InnoDB
         ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180503_133114_alter_RoomCV cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180503_133114_alter_RoomCV cannot be reverted.\n";

        return false;
    }
    */
}
