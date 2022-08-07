<?php

use yii\db\Migration;

class m171129_200456_add_jobOffer_tables extends Migration
{
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `JobOffer` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `contactEmail` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
              `salary` varchar(255) DEFAULT NULL,
              `currency` enum('eur','usd') COLLATE utf8_unicode_ci DEFAULT NULL,
              `contractType` enum('cdi','cdd','stage') COLLATE utf8_unicode_ci NOT NULL,
              `startDate` date DEFAULT NULL,
              `expiryDate` date DEFAULT NULL,
              `isRemoved` tinyint(1) NOT NULL DEFAULT '0',
              `createdDate` datetime NOT NULL,
              `updatedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

            CREATE TABLE IF NOT EXISTS `JobOfferLang` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `jobOfferID` int(11) NOT NULL,
              `languageID` char(2) COLLATE utf8_unicode_ci NOT NULL,
              `title` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
              `description` text COLLATE utf8_unicode_ci NOT NULL,
              `skills` text COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_JobOfferLang_JobOffer1_idx` (`jobOfferID`),
              KEY `fk_JobOfferLang_Language1_idx` (`languageID`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

            ALTER TABLE `JobOfferLang`
              ADD CONSTRAINT `fk_JobOfferLang_JobOffer1` FOREIGN KEY (`jobOfferID`) REFERENCES `JobOffer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              ADD CONSTRAINT `fk_JobOfferLang_Language1` FOREIGN KEY (`languageID`) REFERENCES `Language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");
    }

    public function safeDown()
    {
        echo "m171129_200456_add_jobOffer_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171129_200456_add_jobOffer_tables cannot be reverted.\n";

        return false;
    }
    */
}
