<?php

use yii\db\Migration;

/**
 * Class m180503_072546_alter_RoomAccessRequestCV
 */
class m180503_072546_alter_RoomAccessRequestCV extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP TABLE RoomAccessRequestCV;

            CREATE TABLE IF NOT EXISTS `RoomAccessRequestCV` (
              `accessRequestID` INT NOT NULL,
              `agreementID` INT NOT NULL,
              PRIMARY KEY (`accessRequestID`),
              INDEX `fk_RoomAccessRequestCV_Document1_idx` (`agreementID` ASC),
              CONSTRAINT `fk_RoomAccessRequestCV_RoomAccessRequest1`
                FOREIGN KEY (`accessRequestID`)
                REFERENCES `RoomAccessRequest` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomAccessRequestCV_Document1`
                FOREIGN KEY (`agreementID`)
                REFERENCES `Document` (`id`)
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
        echo "m180503_072546_alter_RoomAccessRequestCV cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180503_072546_alter_RoomAccessRequestCV cannot be reverted.\n";

        return false;
    }
    */
}
