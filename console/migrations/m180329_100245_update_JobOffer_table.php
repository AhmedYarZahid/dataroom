<?php

use yii\db\Migration;

class m180329_100245_update_JobOffer_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE JobOffer
            ADD publicationDate date NULL AFTER startDate,
            CHANGE contractType contractType varchar(255) NOT NULL;

            UPDATE JobOffer
            SET publicationDate = startDate;

            ALTER TABLE JobOfferLang
            ADD location varchar(255) NULL AFTER title;
        ");
    }

    public function safeDown()
    {
        echo "m180329_100245_update_JobOffer_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180329_100245_update_JobOffer_table cannot be reverted.\n";

        return false;
    }
    */
}
