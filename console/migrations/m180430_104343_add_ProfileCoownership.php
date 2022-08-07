<?php

use yii\db\Migration;

/**
 * Class m180430_104343_add_ProfileCoownership
 */
class m180430_104343_add_ProfileCoownership extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `ProfileCoownership` (
              `userID` INT NOT NULL,
              `propertyType` TINYINT(4) UNSIGNED NOT NULL,
              `lotsNumber` INT NOT NULL,
              `coownersNumber` INT NULL DEFAULT NULL,
              PRIMARY KEY (`userID`),
              CONSTRAINT `fk_ProfileCoownership_User1`
                FOREIGN KEY (`userID`)
                REFERENCES `User` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `ProfileCoownership2Region` (
              `profileCoownershipID` INT NOT NULL,
              `regionID` SMALLINT NOT NULL,
              PRIMARY KEY (`profileCoownershipID`, `regionID`),
              INDEX `fk_ProfileRealEstate2Region_Region1_idx` (`regionID` ASC),
              CONSTRAINT `fk_ProfileCoownership2Region_ProfileCoownership1`
                FOREIGN KEY (`profileCoownershipID`)
                REFERENCES `ProfileCoownership` (`userID`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ProfileCoownership2Region_Region1`
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
        echo "m180430_104343_add_ProfileCoownership cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180430_104343_add_ProfileCoownership cannot be reverted.\n";

        return false;
    }
    */
}
