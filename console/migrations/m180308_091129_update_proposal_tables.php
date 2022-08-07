<?php

use yii\db\Migration;

class m180308_091129_update_proposal_tables extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE Proposal
            ADD creatorID int(11) NOT NULL AFTER userID;

            UPDATE Proposal
            SET creatorID = userID;
            
            ALTER TABLE Proposal
            ADD FOREIGN KEY (`creatorID`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

            ALTER TABLE ProposalCompany
            ADD documentID int(11) NULL,
            ADD totalAmount varchar(50) NULL,
            ADD tangibleAmount varchar(50) NULL,
            ADD intangibleAmount varchar(50) NULL,
            ADD stock varchar(255) NULL,
            ADD otherCharges varchar(50) NULL,
            ADD N642 varchar(50) NULL,
            ADD paidLeave tinyint(1) NOT NULL DEFAULT 0,
            ADD other text NULL,
            ADD employersNumber varchar(50) NULL;
        ");
    }

    public function safeDown()
    {
        echo "m180308_091129_update_proposal_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180308_091129_update_proposal_tables cannot be reverted.\n";

        return false;
    }
    */
}
