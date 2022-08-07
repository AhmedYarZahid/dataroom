<?php

use yii\db\Migration;

/**
 * Class m180510_163246_add_ProfileCV
 */
class m180510_163246_add_ProfileCV extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `ProfileCV` (
              `userID` INT NOT NULL,
              PRIMARY KEY (`userID`),
              CONSTRAINT `fk_ProfileCV_User1`
                FOREIGN KEY (`userID`)
                REFERENCES `User` (`id`)
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
        echo "m180510_163246_add_ProfileCV cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180510_163246_add_ProfileCV cannot be reverted.\n";

        return false;
    }
    */
}
