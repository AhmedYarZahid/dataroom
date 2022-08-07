<?php

use yii\db\Migration;

class m180319_073011_update_ProposalCompany extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE ProposalCompany
            DROP COLUMN totalAmount,
            DROP COLUMN otherCharges,
            ADD workInProgress varchar(255) NULL AFTER stock,
            CHANGE N642 loansRecovery varchar(255) NULL;
        ");
    }

    public function safeDown()
    {
        echo "m180319_073011_update_ProposalCompany cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180319_073011_update_ProposalCompany cannot be reverted.\n";

        return false;
    }
    */
}
